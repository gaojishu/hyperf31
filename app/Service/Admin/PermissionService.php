<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Model\Admin\Permission;

class PermissionService
{
    public function findAll()
    {
        return Permission::query()->orderBy('sort', 'asc')->get();
    }
}
