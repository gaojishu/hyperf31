<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Exception\BusinessException;
use App\Model\Admin\Role;

class RoleService
{
    public function findAll()
    {
        return Role::query()->orderBy('id', 'asc')->get();
    }

    public function store(array $data): Role
    {
        $role = Role::query()->where('id', $data['id'] ?? null)->first();

        if (! $role) {
            $role = new Role;
        }
        unset($data['id']);

        foreach ($data as $key => $value) {
            $role->$key = $value;
        }
        $role->save();
        return $role;
    }

    public function delete(int $id): void
    {
        Role::query()->where('id', $id)->delete();
    }
}
