<?php

namespace App\Services;

use App\Models\User;
use App\Helpers\AuditLogger;
use Illuminate\Support\Facades\Log;

class SyncUsersFromIdp
{
    protected IdpService $idp;

    public function __construct(IdpService $idp)
    {
        $this->idp = $idp;
    }

    /**
     * Execute the bulk UUID sync from Identity Provider.
     *
     * Fetches all users from the IDP admin API, matches them to local
     * accounts by email, and backfills missing UUIDs or updates changed info.
     * Never creates new accounts — only updates existing ones.
     *
     * @return array Summary with keys: linked, updated, skipped, errors
     */
    public function execute(): array
    {
        $summary = [
            'linked'  => 0,  // Users who had no UUID and got one backfilled
            'updated' => 0,  // Users who already had UUID but had info changes
            'skipped' => 0,  // IDP users with no matching local account
            'errors'  => [],
        ];

        // 1. Obtain admin access token via Client Credentials grant
        try {
            $accessToken = $this->idp->getClientCredentialsToken();
            Log::info('IDP UUID Sync: Successfully obtained admin access token.');
        } catch (\Exception $e) {
            $summary['errors'][] = 'Failed to obtain admin access token: ' . $e->getMessage();
            Log::error('IDP UUID Sync: Failed to obtain admin access token', [
                'error' => $e->getMessage(),
            ]);
            return $summary;
        }

        // 2. Paginate through all IDP users
        $page = 1;
        $limit = 100;
        $totalProcessed = 0;

        do {
            try {
                $response = $this->idp->getAdminUserList($accessToken, $page, $limit);
            } catch (\Exception $e) {
                $summary['errors'][] = 'Failed to fetch page ' . $page . ': ' . $e->getMessage();
                Log::error('IDP UUID Sync: Failed to fetch user list page', [
                    'page'  => $page,
                    'error' => $e->getMessage(),
                ]);
                break;
            }

            // The IDP response structure may vary — handle common formats
            $users = $response['data'] ?? $response['users'] ?? $response;

            // If the response is not an array of users, try to extract from nested structure
            if (!is_array($users) || empty($users)) {
                Log::info('IDP UUID Sync: No more users returned on page ' . $page . '. Stopping pagination.');
                break;
            }

            // Filter out non-user entries (in case the response includes metadata)
            if (isset($users[0]) && is_array($users[0])) {
                // Looks like an array of user objects — proceed
            } else {
                // Not a list of users — might be a single-level response with metadata
                Log::warning('IDP UUID Sync: Unexpected response structure on page ' . $page, [
                    'keys' => array_keys($response),
                ]);
                break;
            }

            foreach ($users as $idpUser) {
                $this->processIdpUser($idpUser, $summary);
                $totalProcessed++;
            }

            $page++;

            // Safety: stop if we've processed an unreasonable number (prevent infinite loops)
            if ($totalProcessed > 10000) {
                Log::warning('IDP UUID Sync: Safety limit reached (10,000 users). Stopping.');
                break;
            }

            // If we got fewer results than the limit, we've reached the last page
            if (count($users) < $limit) {
                break;
            }

        } while (true);

        // 3. Log final summary
        Log::info('IDP UUID Sync completed', [
            'total_processed' => $totalProcessed,
            'linked'          => $summary['linked'],
            'updated'         => $summary['updated'],
            'skipped'         => $summary['skipped'],
            'errors_count'    => count($summary['errors']),
        ]);

        AuditLogger::log(
            'IDP UUID Sync',
            'sync',
            "Bulk UUID sync completed: {$summary['linked']} linked, {$summary['updated']} updated, {$summary['skipped']} skipped."
        );

        return $summary;
    }

    /**
     * Process a single IDP user record.
     *
     * @param  array  $idpUser  The user record from IDP admin API.
     * @param  array  &$summary  Running summary counters.
     */
    protected function processIdpUser(array $idpUser, array &$summary): void
    {
        $idpUserId = $idpUser['id'] ?? $idpUser['user_id'] ?? null;
        $idpEmail  = $idpUser['email'] ?? null;

        // Skip if IDP record is missing critical fields
        if (empty($idpUserId) || empty($idpEmail)) {
            $summary['skipped']++;
            return;
        }

        $idpEmail = strtolower(trim($idpEmail));

        // Find local user by email (case-insensitive)
        $localUser = User::whereRaw('LOWER(email) = ?', [$idpEmail])->first();

        if (!$localUser) {
            // No matching local account — skip (never create new accounts)
            $summary['skipped']++;
            return;
        }

        try {
            $changed = false;
            $wasLinked = false;

            // Backfill UUID if missing
            if (empty($localUser->idp_user_id)) {
                $localUser->idp_user_id = $idpUserId;
                $changed = true;
                $wasLinked = true;

                Log::info('IDP UUID Sync: Linked UUID for user', [
                    'user_id'     => $localUser->id,
                    'email'       => $localUser->email,
                    'idp_user_id' => $idpUserId,
                    'role'        => $localUser->role,
                ]);
            } elseif ($localUser->idp_user_id !== $idpUserId) {
                // UUID mismatch — log warning but don't overwrite
                Log::warning('IDP UUID Sync: UUID mismatch for user', [
                    'user_id'            => $localUser->id,
                    'email'              => $localUser->email,
                    'local_idp_user_id'  => $localUser->idp_user_id,
                    'remote_idp_user_id' => $idpUserId,
                ]);
                // Don't count as linked or updated — this is a conflict
                return;
            }

            // Check for name/email updates from IDP
            $idpFirstName  = $idpUser['first_name'] ?? null;
            $idpMiddleName = $idpUser['middle_name'] ?? null;
            $idpLastName   = $idpUser['last_name'] ?? null;

            if ($idpFirstName && $localUser->first_name !== $idpFirstName) {
                $localUser->first_name = $idpFirstName;
                $changed = true;
            }

            if ($idpMiddleName !== null && $localUser->middle_name !== $idpMiddleName) {
                $localUser->middle_name = $idpMiddleName;
                $changed = true;
            }

            if ($idpLastName && $localUser->last_name !== $idpLastName) {
                $localUser->last_name = $idpLastName;
                $changed = true;
            }

            // Update full_name if name components changed
            if ($changed && $idpFirstName && $idpLastName) {
                $newFullName = trim($idpFirstName . ' ' . $idpLastName);
                if ($localUser->full_name !== $newFullName) {
                    $localUser->full_name = $newFullName;
                }
            }

            if ($changed) {
                $localUser->save();

                if ($wasLinked) {
                    $summary['linked']++;
                } else {
                    $summary['updated']++;
                    Log::info('IDP UUID Sync: Updated user info from IDP', [
                        'user_id' => $localUser->id,
                        'email'   => $localUser->email,
                    ]);
                }
            }

        } catch (\Exception $e) {
            $summary['errors'][] = "Error processing {$idpEmail}: " . $e->getMessage();
            Log::error('IDP UUID Sync: Error processing user', [
                'email' => $idpEmail,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
