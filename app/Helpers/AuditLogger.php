<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function log(
        string $module,
        string $action,
        string $description,
        $affectedUserId = null
    ) {
        $actor = Auth::user();

        // Make sure the affected user exists
        $affectedName = null;
        if ($affectedUserId) {
            $affectedUser = User::find($affectedUserId);
            $affectedName = $affectedUser ? $affectedUser->full_name : 'Unknown';
        }

        // Sanitize values to strings to prevent array errors
        $module = is_array($module) ? json_encode($module) : (string) $module;
        $action = is_array($action) ? json_encode($action) : (string) $action;
        $description = is_array($description) ? json_encode($description) : (string) $description;

        AuditLog::create([
            'user_id'          => null,
            'user_name'        => $affectedName, 
            'user_role'        => $actor ? $actor->role : 'unknown',
            'affected_user_id' => $affectedUserId,
            'affected_name'    => $affectedName,
            'module'           => $module,
            'action'           => $action,
            'description'      => $description,
            'ip_address'       => request()->ip(),
        ]);
    }
}