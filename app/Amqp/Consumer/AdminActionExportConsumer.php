<?php

declare(strict_types=1);

namespace App\Amqp\Consumer;

use App\Service\Admin\Action\AdminActionExport;
use Hyperf\Amqp\Result;
use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Context\ApplicationContext;
use PhpAmqpLib\Message\AMQPMessage;

#[
    Consumer(
        exchange: 'amdin.action.export.direct',
        routingKey: 'amdin.action.export',
        queue: 'amdin.action.export.que',
        name: "AdminActionConsumer",
        nums: 1
    )
]
class AdminActionExportConsumer extends ConsumerMessage
{

    public function consumeMessage($data, AMQPMessage $message): Result
    {
        try {
            $handler = ApplicationContext::getContainer()->get(AdminActionExport::class);
            $handler->handle($data);
            return Result::ACK;
        } catch (\Throwable $e) {
            var_dump($e);
            return Result::DROP;
        }
    }
}
