<?php

declare(strict_types=1);

namespace App\Listener;

use App\Enum\Admin\AsycnJob\AsyncJobQueueEnum;
use App\Event\AsyncJobCreateOrRetryEvent;
use Hyperf\Amqp\Producer;
use Hyperf\Context\ApplicationContext;
use Hyperf\Event\Annotation\Listener;
use Psr\Container\ContainerInterface;
use Hyperf\Event\Contract\ListenerInterface;

use function Hyperf\Support\make;

#[Listener]
class QueueProducerListener implements ListenerInterface
{


    public function __construct(protected ContainerInterface $container) {}

    public function listen(): array
    {
        //
        return [
            AsyncJobCreateOrRetryEvent::class,
        ];
    }

    public function process(object $event): void
    {
        $job = $event->job;

        switch ($job->queue) {
            case AsyncJobQueueEnum::AMQP:
                $jobClass = make($job->job_class, ['data' => $job->payload]);
                $producer = ApplicationContext::getContainer()->get(Producer::class);
                $producer->produce($jobClass);
                break;

            case AsyncJobQueueEnum::REIDS:

                break;

            default:
                throw new \App\Exception\BusinessException("Unsupported queue driver: {$job->queue->value}");
        }
    }
}
