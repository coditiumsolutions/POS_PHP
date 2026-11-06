<?php

namespace App\Services;

use App\Models\User;

class RoleModuleAccess
{
    public static function canAccess(?string $controller, ?User $user): bool
    {
        if (!$user) {
            return false;
        }
        // Local POS_PHP: admins see everything; others see all non-admin modules.
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }
        $role = (string) ($user->role_name ?? '');
        $adminish = ['Administrator', 'Admin', 'administrator', 'admin'];
        if (in_array($role, $adminish, true)) {
            return true;
        }
        $adminOnly = ['Users', 'Setup', 'Configurations', 'AuditLogs', 'Branches', 'Warehouses', 'Terminals', 'OfflineSyncTables'];
        if (in_array($controller, $adminOnly, true)) {
            return false;
        }
        return true;
    }
}
