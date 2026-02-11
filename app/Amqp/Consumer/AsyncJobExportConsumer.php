<?php

declare(strict_types=1);

namespace App\Amqp\Consumer;

use App\Enum\Admin\AsycnJob\AsyncJobStatusEnum;
use App\Exception\BusinessException;
use App\Model\Admin\AsyncJob;
use App\Service\Admin\Action\AsyncJobExport;
use Carbon\Carbon;
use Hyperf\Amqp\Result;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Context\ApplicationContext;
use Hyperf\DbConnection\Db;
use PhpAmqpLib\Message\AMQPMessage;

#[Consumer(exchange: 'async.job.export.direct', routingKey: 'async.job.export', queue: 'async.job.export.que', name: "AdminActionConsumer", nums: 1)]
class AsyncJobExportConsumer extends ConsumerMessage
{
    public function consumeMessage($data, AMQPMessage $message): Result
    {
        /** @var AsyncJob|null $job */
        $job = null;
        $uuid = $data['uuid'] ?? null;

        try {
            if (!$uuid) {
                return Result::DROP;
            }

            // 1. 锁定并更新状态（快进快出，释放行锁）
            Db::beginTransaction();
            try {
                $job = AsyncJob::where('uuid', $uuid)->lockForUpdate()->first();

                if (!$job) {
                    throw new BusinessException("任务不存在: {$uuid}");
                }

                // 校验重试次数：如果已达到最大值，直接丢弃
                if ($job->attempts >= $job->max_attempts) {
                    Db::rollBack();
                    return Result::DROP;
                }

                // 只有 PENDING 或 FAILED 的任务可以被执行（允许重试）
                if (!$job->status->isPendingOrFailed()) {
                    // 如果状态不是待处理，说明已被抢占或已完成
                    Db::rollBack();
                    return Result::DROP;
                }

                $job->status = AsyncJobStatusEnum::PROCESSING;
                $job->attempts += 1;
                $job->reserved_at = Carbon::now()->toDateTimeString();
                $job->save();
                Db::commit();
            } catch (\Throwable $e) {
                Db::rollBack();
                throw $e; // 抛出给外层统一处理更新 FAILED 状态
            }

            // 2. 执行耗时业务逻辑（脱离事务执行，避免长时间占用数据库连接）
            $handler = ApplicationContext::getContainer()->get(AsyncJobExport::class);
            $result = $handler->handle($data);

            // 3. 成功更新
            $job->status = AsyncJobStatusEnum::SUCCESS;
            $job->reserved_at = null; // 任务结束，释放锁定标志
            $job->result = $result;
            $job->save();

            return Result::ACK;
        } catch (\Throwable $e) {
            // 4. 异常处理：确保能写回失败状态
            if ($job instanceof AsyncJob) {
                $job->status = AsyncJobStatusEnum::FAILED;
                $job->reserved_at = null; // 任务结束，释放锁定标志
                $job->error_message = mb_substr($e->getMessage(), 0, 2000);
                $job->save();
            }
            return Result::REQUEUE;
        }
    }
}
