<?php

declare(strict_types=1);

namespace App\Model\Admin;

use App\Enum\Admin\AsycnJob\AsyncJobQueueEnum;
use App\Enum\Admin\AsycnJob\AsyncJobStatusEnum;

/**
 * @property int $id 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property array $payload 
 * @property AsyncJobStatusEnum $status 
 * @property int $attempts 
 * @property int $max_attempts 
 * @property \Carbon\Carbon $reserved_at 
 * @property \Carbon\Carbon $available_at 
 * @property string $error_message 
 * @property AsyncJobQueueEnum $queue
 * @property string $job_class 
 * @property string $uuid 
 */
class AsyncJob extends BaseModel
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'async_jobs';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'payload' => 'array',
        'result' => 'array',
        'queue' => AsyncJobQueueEnum::class,
        'status' => AsyncJobStatusEnum::class
    ];
}
