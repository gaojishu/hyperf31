<?php

namespace App\Service\Admin\Action;

use App\Event\AdminNoticeCreateEvent;
use App\Service\Admin\AsyncJobService;
use App\Service\Admin\NoticeService;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;

class AsyncJobExport
{
    #[Inject]
    protected AsyncJobService $async_job_service;

    #[Inject]
    protected ExcelExport $excelExport;

    #[Inject]
    private EventDispatcherInterface $eventDispatcher;

    #[Inject]
    private NoticeService $noticeService;

    const TITLE = '导出异步任务';


    public function handle(array $data): array
    {
        $query_params = $data['params'] ?? null;
        $fileName = \Ramsey\Uuid\Uuid::uuid4() . '.xlsx';

        // 1. 定义数据获取逻辑（通过闭包实现流式写入）
        $dataGenerator = function ($sheet) use ($query_params, &$count) {
            $count = 0;
            $this->async_job_service->queryWhere($query_params)
                ->chunk(1000, function ($actions) use ($sheet, &$count) {
                    $rows = [];
                    foreach ($actions as $item) {
                        $count++;
                        $rows[] = [
                            $item->id,
                            $item?->queue,
                            $item->uuid,
                            $item->job_class,
                            $item->status->label(),
                            $item->attempts,
                            $item->max_attempts,
                            json_encode($item->payload, JSON_UNESCAPED_UNICODE),
                            (string)$item->reserved_at,
                            (string)$item->available_at,
                            json_encode($item->result, JSON_UNESCAPED_UNICODE),
                            $item->error_message,
                            (string)$item->created_at,
                            (string)$item->updated_at,
                        ];
                    }
                    $sheet->data($rows);
                });
            return $count;
        };

        // 2. 调用通用导出服务
        $headers = ['ID', '任务通道', 'uuid', '任务类',  '状态', '次数', '最大重试', '任务参数', '锁定时间', '预计时间',  '执行结果', '错误信息', '创建时间', '更新时间'];
        [$ossPath, $rowCount] = $this->excelExport->exportToOss($fileName, $headers, $dataGenerator);

        // 3. 记录通知
        $this->saveNotice($data['admin_id'], $rowCount, $ossPath);
        return [$ossPath];
    }

    private function saveNotice($adminId, $count, $ossPath): void
    {
        $notice = $this->noticeService->create(
            admin_id: $adminId,
            title: self::TITLE,
            content: "共导出 {$count} 行数据，可在消息中心查看下载",
            att: [$ossPath]
        );

        $this->eventDispatcher->dispatch(new AdminNoticeCreateEvent($notice));
    }
}
