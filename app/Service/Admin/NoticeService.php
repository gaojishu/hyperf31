<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Enum\Admin\SortEnum;
use App\Model\Admin\Notice;

class NoticeService
{

    public function queryWhere(array $data)
    {
        // 1. 预处理参数
        $params = $data['params'] ?? [];
        $sort = $data['sort'] ?? [];

        $query = Notice::query();

        $query->when($params['id'] ?? null, function ($query, $id) {
            $query->where('id', $id);
        }) // 筛选：用户名 模糊匹配 (修正了你代码中的 filter 错误)
            ->when($params['title'] ?? null, function ($query, $title) {
                $query->where('title', 'like', "%{$title}%");
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

    public function create(int $admin_id, string $title, string $content, ?array $att): Notice
    {
        $notice = new Notice();
        $notice->admin_id = $admin_id;
        $notice->title = $title;
        $notice->content = $content;
        $notice->attachments = $att;
        $notice->save();
        return $notice;
    }
}
