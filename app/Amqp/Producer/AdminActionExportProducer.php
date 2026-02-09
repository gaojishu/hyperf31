<?php

declare(strict_types=1);

namespace App\Amqp\Producer;

use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;

#[
    Producer(
        exchange: 'amdin.action.export.direct',
        routingKey: 'amdin.action.export'
    )
]
class AdminActionExportProducer extends ProducerMessage
{
    public function __construct($data)
    {
        $this->payload = $data;
    }
}
