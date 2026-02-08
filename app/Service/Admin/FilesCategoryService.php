<?php

declare(strict_types=1);

namespace App\Service\Admin;

use App\Exception\BusinessException;
use App\Model\Admin\Files;
use App\Model\Admin\FilesCategory;


class FilesCategoryService
{
    public function findAll()
    {
        return FilesCategory::query()->orderBy('id', 'asc')->get();
    }

    public function store(array $data): FilesCategory
    {
        $model = FilesCategory::query()->where('id', $data['id'] ?? null)->first();

        if (! $model) {
            $model = new FilesCategory;
        }
        unset($data['id']);

        foreach ($data as $key => $value) {
            $model->$key = $value;
        }
        $model->save();
        return $model;
    }

    public function delete(int $id): void
    {
        $file = Files::where('category_id', $id)->first();
        if ($file) {
            throw new BusinessException(500, '删除失败，此分类下有文件');
        }
        FilesCategory::query()->where('id', $id)->delete();
    }
}
