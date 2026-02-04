<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Model\Admin\Admin;

class AdminService
{
    public function findById(?int $admin_id): ?Admin
    {
        $admin = Admin::where('id', $admin_id)->first();
        return $admin;
    }

    public function findPermissionByAdminId(?int $admin_id)
    {
        $admin = Admin::find($admin_id);
        if (! $admin) {
            return null;
        }
        return $admin->permission;
    }
}
