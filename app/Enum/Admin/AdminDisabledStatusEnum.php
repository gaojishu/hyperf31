<?php

declare(strict_types=1);

namespace App\Enum\Admin;

enum AdminDisabledStatusEnum: int
{
    case ENABLED  = 0; // 启用
    case DISABLED = 1; // 禁用

    /**
     * 获取状态标签（中文描述）
     */
    public function label(): string
    {
        return match ($this) {
            self::ENABLED  => '启用',
            self::DISABLED => '禁用',
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
     * 获取键值对映射 [value => label]
     */
    public static function toArray(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }
        return $result;
    }

    /**
     * 判断是否为启用状态
     */
    public function isEnabled(): bool
    {
        return $this === self::ENABLED;
    }

    /**
     * 判断是否为禁用状态
     */
    public function isDisabled(): bool
    {
        return $this === self::DISABLED;
    }
}
