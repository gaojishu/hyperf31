<?php

declare(strict_types=1);

namespace App\Model\Admin;

use App\Utils\Aliyun\OssUtil;

/**
 * @property int $id 
 * @property \Carbon\Carbon $created_at 
 * @property string $deleted_at 
 * @property \Carbon\Carbon $updated_at 
 * @property int $admin_id 
 * @property string $title 
 * @property string $content 
 * @property array $attachments 
 */
class Notice extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'notice';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['attachments' => 'array'];


    public function admin()
    {
        return $this->hasOne(Admin::class, 'id', 'admin_id');
    }

    public function getAttachmentsAttribute($value)
    {

        $res = [];
        if ($value) {
            $att = json_decode($value, true);
            foreach ($att as $val) {
                $res[] = OssUtil::generatePresignedUrl($val);
            }
        }
        return $res ?? null;
    }
}
