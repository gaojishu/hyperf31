<?php

declare(strict_types=1);

namespace App\Model\Admin;

use App\Enum\Admin\PermissionTypeEnum;
use App\Model\BaseModel;

/**
 * @property int $id 
 * @property \Carbon\Carbon $created_at 
 * @property string $deleted_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $icon 
 * @property int $level 
 * @property string $name 
 * @property int $parent_id 
 * @property string $path 
 * @property string $remark 
 * @property int $sort 
 * @property PermissionTypeEnum $type 
 * @property string $code 
 * @property string $key 
 */
class Permission extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'permission';

    protected array $casts = ['type' => PermissionTypeEnum::class];
}
