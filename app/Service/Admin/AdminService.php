<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Model\Admin\Admin;

class AdminService
{
    public function findById(?int $adminId): ?Admin
    {
        $admin = Admin::where('id', $adminId)->first();
        return $admin;
    }

    public function findPermissionByAdminId(?int $adminId)
    {
        $admin = Admin::find($adminId);
        if (! $admin) {
            return null;
        }
        return $admin->permission;
    }
}
