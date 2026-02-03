<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\BusinessException;
use App\Util\HttpResponse\HttpResponse;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GlobalExceptionHandler extends ExceptionHandler
{
    use HttpResponse;
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        // 处理 BusinessException
        if ($throwable instanceof BusinessException) {

            // 构造统一响应格式
            $data = $this->apidata($throwable->getMessage(), $throwable->getCode());

            // 返回 JSON 响应
            return $response
                ->withStatus(500) // 业务异常通常仍返回 200，由 code 判断是否成功
                ->withHeader('Content-Type', 'application/json')
                ->withBody(new SwooleStream(json_encode($data, JSON_UNESCAPED_UNICODE)));
        }

        var_dump('throwable instanceof:' . get_class($throwable));

        if ($throwable instanceof \Hyperf\Validation\ValidationException) {
            // 构造统一响应格式
            $data = $this->setData($throwable->validator->errors()->getMessages())->apidata($throwable->validator->errors()->first(), 400);

            // 响应
            return $response
                ->withStatus(422)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(new SwooleStream(json_encode($data, JSON_UNESCAPED_UNICODE)));
        }

        return $this->handle($throwable, $response);
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
