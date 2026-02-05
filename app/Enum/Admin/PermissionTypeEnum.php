<?php

declare(strict_types=1);

namespace App\Enum\Admin;

enum PermissionTypeEnum: int
{
    case MENU_PERMISSION  = 1; // 菜单权限
    case OPERATION_PERMISSION = 2; // 操作权限

    /**
     * 获取状态标签（中文描述）
     */
    public function label(): string
    {
        return match ($this) {
            self::MENU_PERMISSION  => '菜单权限',
            self::OPERATION_PERMISSION => '操作权限',
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


    public function isMenuPermission(): bool
    {
        return $this === self::MENU_PERMISSION;
    }

    public function isOperationPermission(): bool
    {
        return $this === self::OPERATION_PERMISSION;
    }
}
