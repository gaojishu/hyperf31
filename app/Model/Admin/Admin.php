<?php

declare(strict_types=1);

namespace App\Model\Admin;

use App\Enum\Admin\AdminDisabledStatusEnum;

/**
 * @property int $id 
 * @property \Carbon\Carbon $created_t 
 * @property string $deleted_at 
 * @property \Carbon\Carbon $updated_t 
 * @property string $email 
 * @property AdminDisabledStatusEnum $disabled_status 
 * @property string $mobile 
 * @property string $nickname 
 * @property string $password 
 * @property string $username 
 * @property array $permission_key 
 * @property-read null|\Hyperf\Database\Model\Collection|Permission[] $permission 
 */
class Admin extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin';

    protected array $hidden = ['password'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['permission_key' => 'array', 'disabled_status' => AdminDisabledStatusEnum::class];


    public function permission()
    {
        return $this->belongsToMany(Permission::class, 'admin_permission',  'admin_id', 'permission_id')->orderBy('sort');
    }
}
