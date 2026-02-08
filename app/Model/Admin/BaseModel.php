<?php

declare(strict_types=1);

namespace App\Model\Admin;

use App\Model\Model;

class BaseModel extends Model
{
    protected string $schema = 'admin';

    public function getTable(): string
    {
        $table = parent::getTable();
        // 只有当表名不含 Schema 且定义了非 public 的 schema 时才拼接
        if (! str_contains($table, '.') && $this->schema !== 'public') {
            return "{$this->schema}.{$table}";
        }

        return $table;
    }
}
