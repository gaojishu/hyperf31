<?php

declare(strict_types=1);

namespace App\Model\Admin;

use App\Model\BaseModel;

/**
 * @property int $id 
 * @property \Carbon\Carbon $created_at 
 * @property string $deleted_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $name 
 * @property string $remark 
 * @property array $permission_key 
 */
class Role extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'role';

    protected array $casts = ['permission_key' => 'array'];
}
