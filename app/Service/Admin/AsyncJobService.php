<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Amqp\Producer\AsyncJobExportProducer;
use App\Enum\Admin\AsycnJob\AsyncJobQueueEnum;
use App\Enum\Admin\SortEnum;
use App\Event\AsyncJobCreateOrRetryEvent;
use App\Model\Admin\AsyncJob;
use App\Utils\Auth\Auth;
use Carbon\Carbon;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Ramsey\Uuid\Uuid;

class AsyncJobService
{
    #[Inject()]
    private EventDispatcherInterface $eventDispatcher;

    public function queryWhere(array $data)
    {
        // 1. 预处理参数
        $params = $data['params'] ?? [];
        $sort = $data['sort'] ?? [];

        $query = AsyncJob::query();

        $query->when($params['id'] ?? null, function ($query, $id) {
            $query->where('id', $id);
        }) // 筛选：用户名 模糊匹配 (修正了你代码中的 filter 错误)
            ->when($params['queue'] ?? null, function ($query, $queue) {
                $query->where('queue', 'like', "%{$queue}%");
            })
            ->when($params['job_class'] ?? null, function ($query, $job_class) {
                $query->where('job_class', 'like', "%{$job_class}%");
            })
            ->when($params['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($params['created_at'] ?? null, function ($query, $created_at) {
                $query->whereBetween('created_at', $created_at);
            })
            ->when($params['updated_at'] ?? null, function ($query, $updated_at) {
                $query->whereBetween('updated_at', $updated_at);
            })
            // 排序：安全处理枚举
            ->when($sort['id'] ?? null, function ($query, $idSort) {
                $direction = SortEnum::tryFrom($idSort)?->sql() ?? 'desc';
                $query->orderBy('id', $direction);
            }, function ($query) {
                $query->orderBy('id', 'desc');
            });
        return $query;
    }

    public function page(array $data)
    {
        // 1. 预处理参数
        $params = $data['params'] ?? [];
        $sort = $data['sort'] ?? [];

        // 2. 分页大小限制 (1-20 范围)
        $pagesize = (int)($params['page_size'] ?? 10);
        $pagesize = max(1, min($pagesize, 20));

        // 2. 分页大小限制 (1-20 范围)
        $pagesize = (int)($params['page_size'] ?? 10);
        $pagesize = max(1, min($pagesize, 20));

        $query = $this->queryWhere($data);


        return $query->paginate($pagesize, ['*'], 'params.current');
    }

    public function export(array $data)
    {
        Db::transaction(function () use ($data) {
            $className = AsyncJobExportProducer::class;

            $uuid = Uuid::uuid4()->toString();
            $payload = [
                'admin_id' => Auth::guard(Auth::GUARD_ADMIN)->getUserId(),
                'params' => $data,
                'uuid' => $uuid
            ];
            $this->create(
                payload: $payload,
                queue: AsyncJobQueueEnum::AMQP,
                job_class: $className,
                uuid: $uuid
            );
        });
    }

    public function create(
        array $payload,
        AsyncJobQueueEnum $queue,
        string $job_class,
        string $uuid,
        ?string $available_at = null
    ): AsyncJob {
        $job = new AsyncJob();
        $job->available_at = $available_at;
        if (!$available_at) {
            $job->available_at = Carbon::now()->toDateTimeString();
        }
        $job->queue = $queue;
        $job->payload = $payload;
        $job->job_class = $job_class;
        $job->uuid = $uuid;
        $job->save();
        $this->eventDispatcher->dispatch(new AsyncJobCreateOrRetryEvent($job));
        return $job;
    }
}
