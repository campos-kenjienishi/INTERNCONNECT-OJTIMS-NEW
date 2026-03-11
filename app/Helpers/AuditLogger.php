<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Session;

class AuditLogger
{
    private static array $roleLabels = [
        '0' => 'Student',
        '1' => 'OJT Coordinator',
        '2' => 'Professor',
    ];

    public static function log(
        string $module,
        string $action,
        string $description,
        $affectedUserId = null
    ) {
        // Get the actor from session (this app uses session-based auth, not Auth::user())
        $actor = null;
        if (Session::has('loginId')) {
            $actor = User::find(Session::get('loginId'));
        }

        // Get the affected user's name
        $affectedName = null;
        if ($affectedUserId) {
            $affectedUser = User::find($affectedUserId);
            $affectedName = $affectedUser ? $affectedUser->full_name : 'Unknown';
        }

        // Sanitize values to strings to prevent array errors
        $module = is_array($module) ? json_encode($module) : (string) $module;
        $action = is_array($action) ? json_encode($action) : (string) $action;
        $description = is_array($description) ? json_encode($description) : (string) $description;

        // Map role number to readable label
        $roleLabel = 'Unknown';
        if ($actor) {
            $roleLabel = self::$roleLabels[(string) $actor->role] ?? 'Unknown';
        }

        AuditLog::create([
            'user_id'          => $actor ? $actor->id : null,
            'user_name'        => $actor ? $actor->full_name : 'System',
            'user_role'        => $roleLabel,
            'affected_user_id' => $affectedUserId,
            'affected_name'    => $affectedName,
            'module'           => $module,
            'action'           => $action,
            'description'      => $description,
            'ip_address'       => request()->ip(),
        ]);
    }
}