<?php

namespace App\Service\Admin\Action;

use App\Event\AdminNoticeCreateEvent;
use App\Service\Admin\AdminActionService;
use App\Model\Admin\Notice;
use App\Service\Admin\NoticeService;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;

class AdminActionExport
{
    #[Inject]
    protected AdminActionService $adminActionService;

    #[Inject]
    protected ExcelExport $excelExport;

    #[Inject]
    private EventDispatcherInterface $eventDispatcher;

    #[Inject]
    private NoticeService $noticeService;


    public function handle(array $data): void
    {
        $query_params = $data['query'] ?? null;
        $fileName = 'admin_action_' . \Ramsey\Uuid\Uuid::uuid4() . '.xlsx';

        // 1. 定义数据获取逻辑（通过闭包实现流式写入）
        $dataGenerator = function ($sheet) use ($query_params, &$count) {
            $count = 0;
            $this->adminActionService->queryWhere($query_params)->with(['admin'])
                ->chunk(1000, function ($actions) use ($sheet, &$count) {
                    $rows = [];
                    foreach ($actions as $item) {
                        $count++;
                        $rows[] = [
                            $item->id,
                            $item?->admin?->username,
                            $item->duration,
                            $item->ip,
                            $item->method,
                            $item->remark,
                            $item->path,
                            json_encode($item->params, JSON_UNESCAPED_UNICODE),
                            json_encode($item->query_params, JSON_UNESCAPED_UNICODE),
                            (string)$item->created_at,
                        ];
                    }
                    $sheet->data($rows);
                });
            return $count;
        };

        // 2. 调用通用导出服务
        $headers = ['ID', '管理员', '时长', 'ip', '方法', '描述', '地址', '参数', 'query 参数', '创建时间'];
        [$ossPath, $rowCount] = $this->excelExport->exportToOss($fileName, $headers, $dataGenerator);

        // 3. 记录通知
        $this->saveNotice($data['admin_id'], $rowCount, $ossPath);
    }

    private function saveNotice($adminId, $count, $ossPath): void
    {
        $notice = $this->noticeService->create(
            admin_id: $adminId,
            title: '导出日志',
            content: "共导出 {$count} 行数据，可在消息中心查看下载",
            att: [$ossPath]
        );

        $this->eventDispatcher->dispatch(new AdminNoticeCreateEvent($notice));
    }
}
