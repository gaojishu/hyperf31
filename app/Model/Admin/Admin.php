<?php

declare(strict_types=1);

namespace App\Model\Admin;

use App\Enum\Admin\AdminDisabledStatusEnum;
use App\Model\BaseModel;

/**
 * @property int $id 
 * @property \Carbon\Carbon $created_at 
 * @property string $deleted_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $email 
 * @property AdminDisabledStatusEnum $disabled_status 
 * @property string $mobile 
 * @property string $nickname 
 * @property string $password 
 * @property string $username 
 * @property string $permission_key 
 */
class Admin extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'created_at', 'deleted_at', 'updated_at', 'email', 'disabled_status', 'mobile', 'nickname', 'password', 'username', 'permission_key'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'disabled_status' => AdminDisabledStatusEnum::class,
        'permission_key' => 'array'
    ];


    public function permission()
    {
        return $this->belongsToMany(Permission::class, 'admin_permission',  'admin_id', 'permission_id')->orderBy('sort');
    }
}
