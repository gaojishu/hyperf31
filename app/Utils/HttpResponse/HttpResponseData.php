<?php

namespace App\Utils\HttpResponse;

use Hyperf\Context\Context;
use Ramsey\Uuid\Uuid;

class HttpResponseData
{

    const CONTEXT_KEY = 'http_response_data';


    protected function getReqId(): string
    {
        return Context::getOrSet('req_id', fn() => Uuid::uuid4()->toString());
    }

    public static function getResponseContext(): array
    {
        return Context::getOrSet(self::CONTEXT_KEY, [
            'code' => 500,
            'success' => false,
            'message' => '',
            'reqId' => Context::getOrSet('req_id', fn() => Uuid::uuid4()->toString()),
            'data' => null,
            'httpStatus' => 'fail'
        ]);
    }

    /**
     * 更新上下文中的数据
     */
    public static function setResponseContext(array $data): void
    {
        Context::set(self::CONTEXT_KEY, $data);
    }

    public static function apidata(string $message = '系统错误', int $code = 500)
    {

        $res = self::getResponseContext();
        $res['code'] = $code;
        $res['message'] = $message;
        $res['success'] = false;
        $res['httpStatus'] = 'fail';

        return $res;
    }
}
