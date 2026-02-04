<?php

declare(strict_types=1);

namespace App\Model\Admin;

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
 * @property int $type 
 * @property string $code 
 * @property string $key 
 */
class Permission extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'permission';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'created_at', 'deleted_at', 'updated_at', 'icon', 'level', 'name', 'parent_id', 'path', 'remark', 'sort', 'type', 'code', 'key'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'level' => 'integer', 'parent_id' => 'integer', 'sort' => 'integer', 'type' => 'integer'];
}
