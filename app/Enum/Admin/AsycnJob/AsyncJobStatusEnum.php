<?php

declare(strict_types=1);

namespace App\Enum\Admin\AsycnJob;

enum AsyncJobStatusEnum: int
{
    case PENDING = 0;   // 待处理
    case PROCESSING = 1; // 处理中
    case SUCCESS = 2;   // 成功
    case FAILED = -1;   // 失败

    /**
     * 获取状态标签（中文描述）
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING    => '待处理',
            self::PROCESSING => '处理中',
            self::SUCCESS    => '成功',
            self::FAILED     => '失败',
        };
    }

    /**
     * 获取所有可用值
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * 转换为数组格式
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label(),
        ];
    }

    /**
     * 获取键值对映射列表
     */
    public static function toArrayList(): array
    {
        return array_map(fn($case) => $case->toArray(), self::cases());
    }

    public function isPendingOrFailed(): bool
    {
        return $this === self::PENDING || $this === self::FAILED;
    }
}
