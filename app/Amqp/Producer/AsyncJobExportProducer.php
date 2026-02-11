<?php

declare(strict_types=1);

namespace App\Amqp\Producer;

use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;

#[
    Producer(
        exchange: 'async.job.export.direct',
        routingKey: 'async.job.export'
    )
]
class AsyncJobExportProducer extends ProducerMessage
{
    public function __construct(array $data)
    {
        $this->payload = $data;
    }
}
