<?php

namespace App\Util\HttpResponse;

use App\Util\Auth\Auth;
use Hyperf\Context\Context;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Ramsey\Uuid\Uuid;

trait HttpResponse
{
    /**
     * @var array 响应模板
     */
    private $responseData = [
        'code' => 500,
        'success' => false,
        'message' => '',
        'reqId' => '',
        'data' => null,
        'httpStatus' => 'fail'
    ];

    /**
     * 必须由使用该 Trait 的类提供 Response 实例
     *
     * @var ResponseInterface
     */
    protected ResponseInterface $response;

    /**
     * 必须由使用该 Trait 的类提供 Response 实例
     *
     * @var RequestInterface
     */
    protected RequestInterface $request;


    public function admin_id()
    {
        $authorization = $this->request->getHeaderLine('Authorization');
        $token = null;

        if (str_starts_with($authorization, 'Bearer ')) {
            $token = substr($authorization, 7);
        }

        if (! $token) {
            $token = $this->request->getQueryParams()['token'] ?? null;
        }

        if (! $token) {
            return null;
        }
        return Auth::guard(Auth::GUARD_ADMIN)->getUserIdByToken($token);
    }

    protected function getReqId(): string
    {
        return Context::getOrSet('req_id', fn() => Uuid::uuid4()->toString());
    }

    public function apisucceed(string $message = '')
    {
        $this->responseData['message'] = $message;
        $this->responseData['success'] = true;
        $this->responseData['code'] = 0;
        $this->responseData['httpStatus'] = 'ok';
        $this->responseData['reqId'] = $this->getReqId();

        return $this->response->json($this->responseData);
    }

    public function apifail(string $message = '系统错误', int $code = 5000)
    {
        $this->responseData['message'] = $message;
        $this->responseData['code'] = $code;
        $this->responseData['success'] = false;
        $this->responseData['reqId'] = $this->getReqId();
        $this->responseData['httpStatus'] = 'fail';

        return $this->response->json($this->responseData);
    }

    public function apidata(string $message = '系统错误', int $code = 5000)
    {
        $this->responseData['message'] = $message;
        $this->responseData['code'] = $code;
        $this->responseData['success'] = false;
        $this->responseData['reqId'] = $this->getReqId();
        $this->responseData['httpStatus'] = 'fail';

        return $this->responseData;
    }

    public function setCode(int $code)
    {
        $this->responseData['code'] = $code;
        return $this;
    }

    public function setMessage(string $message)
    {
        $this->responseData['message'] = $message;
        return $this;
    }

    public function setData(mixed $data)
    {
        $this->responseData['data'] = $data;

        return $this;
    }
}
