<?php

declare(strict_types=1);

namespace App\Model\Admin;

use App\Enum\Admin\Files\FilesTypeEnum;
use App\Utils\Aliyun\OssUtil;

/**
 * @property int $id 
 * @property \Carbon\Carbon $created_at 
 * @property string $deleted_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $key 
 * @property string $mime_type 
 * @property string $name 
 * @property string $remark 
 * @property string $type 
 * @property int $category_id 
 * @property string $hash 
 * @property int $size 
 */
class Files extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'files';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    protected array $casts = ['type' => FilesTypeEnum::class];

    public function getKeyAttribute($value)
    {
        return OssUtil::generatePresignedUrl($value);
    }
}
