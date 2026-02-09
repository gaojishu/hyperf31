<?php

declare(strict_types=1);

namespace App\Event;

use App\Model\Admin\Notice;

class AdminNoticeCreateEvent
{
    // 建议这里定义成 public 属性，以便监听器对该属性的直接使用，或者你提供该属性的 Getter
    public $notice;

    public function __construct(Notice $notice)
    {
        $this->notice = $notice;
    }
}
