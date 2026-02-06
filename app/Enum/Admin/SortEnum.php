<?php

declare(strict_types=1);

namespace App\Enum\Admin;

enum SortEnum: string
{
    case ASC = 'ascend'; // 
    case DESC = 'descend'; // 

    /**
     * 获取状态标签（中文描述）
     */
    public function label(): string
    {
        return match ($this) {
            self::ASC  => '正序',
            self::DESC => '倒序',
        };
    }

    public function sql(): string
    {
        return match ($this) {
            self::ASC  => 'asc',
            self::DESC => 'desc',
        };
    }

    /**
     * 获取所有可用值
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label(),
        ];
    }
    /**
     * 获取键值对映射 [value => label]
     */
    public static function toArrayList(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[] = [
                'value' => $case->value,
                'label' => $case->label(),
            ];
        }
        return $result;
    }
}
