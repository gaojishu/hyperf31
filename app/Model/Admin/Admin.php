<?php

declare(strict_types=1);

namespace App\Model\Admin;

use App\Enum\Admin\AdminDisabledStatusEnum;
use App\Model\BaseModel;

/**
 * @property int $id 
 * @property \Carbon\Carbon $created_t 
 * @property string $deletedAt 
 * @property \Carbon\Carbon $updated_t 
 * @property string $email 
 * @property AdminDisabledStatusEnum $disabled_status 
 * @property string $mobile 
 * @property string $nickname 
 * @property string $password 
 * @property string $username 
 * @property string $permission_key 
 * @property-read null|\Hyperf\Database\Model\Collection|Permission[] $permission 
 */
class Admin extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin';

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'disabled_status' => AdminDisabledStatusEnum::class];


    public function permission()
    {
        return $this->belongsToMany(Permission::class, 'admin_permission',  'admin_id', 'permission_id')->orderBy('sort');
    }
}
