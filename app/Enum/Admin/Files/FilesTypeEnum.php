<?php

declare(strict_types=1);

namespace App\Enum\Admin\Files;

enum FilesTypeEnum: string
{
    case VIDEO  = 'video';
    case IMAGE = 'image';
    case AUDIO = 'audio';
    case OHTER = 'ohter';

    /**
     * 获取状态标签（中文描述）
     */
    public function label(): string
    {
        return match ($this) {
            self::VIDEO  => '视频',
            self::IMAGE => '图片',
            self::AUDIO => '音频',
            self::OHTER => '其他',
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
