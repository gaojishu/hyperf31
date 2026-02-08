<?php

declare(strict_types=1);

namespace App\Model\Admin;

/**
 * @property int $id 
 * @property \Carbon\Carbon $created_at 
 * @property string $deleted_at 
 * @property \Carbon\Carbon $updated_at 
 * @property int $admin_id 
 * @property int $duration 
 * @property string $ip 
 * @property string $method 
 * @property string $remark 
 * @property string $path 
 * @property array $params 
 * @property array $query_params
 */
class AdminAction extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin_action';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['params' => 'array', 'query_params' => 'array'];


    public function admin()
    {
        return $this->hasOne(Admin::class, 'id', 'admin_id');
    }
}
