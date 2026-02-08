<?php

declare(strict_types=1);

namespace App\Model\Admin;

/**
 * @property int $id 
 * @property \Carbon\Carbon $created_at 
 * @property string $deleted_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $name 
 * @property string $remark 
 */
class FilesCategory extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'files_category';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
