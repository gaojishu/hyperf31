<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Exception\BusinessException;
use App\Model\Admin\Admin;

class AdminService
{

    public function page()
    {
        return Admin::orderBy('id', 'desc')->paginate(10);
    }

    public function create(array $data)
    {
        $admin = new Admin();
        foreach ($data as $key => $value) {
            $admin->$key = $value;
        }
        return $admin->save();
    }

    public function update(array $data)
    {
        $admin = Admin::where('id', $data['id'])->first();
        if (! $admin) {
            throw new BusinessException(500, '数据不存在');
        }
        unset($data['id']);
        foreach ($data as $key => $value) {
            $admin->$key = $value;
        }
        return $admin->save();
    }

    public function findById(?int $admin_id): ?Admin
    {
        $admin = Admin::where('id', $admin_id)->first();
        return $admin;
    }

    public function findPermissionByadmin_id(?int $admin_id)
    {
        $admin = Admin::find($admin_id);
        if (! $admin) {
            return null;
        }
        return $admin->permission;
    }
}
