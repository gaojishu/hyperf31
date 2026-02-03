<?php

namespace App\Util\HttpResponse;

use Hyperf\Context\Context;
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
        'message' => 'fail',
        'reqId' => '',
        'data' => null,
    ];

    /**
     * 必须由使用该 Trait 的类提供 Response 实例
     *
     * @var ResponseInterface
     */
    protected ResponseInterface $response;

    public function admin_id()
    {
        return Context::get('user_id');
    }

    protected function getReqId(): string
    {
        return Context::getOrSet('req_id', fn() => Uuid::uuid4()->toString());
    }

    public function apisucceed(string $message = '操作成功')
    {
        $this->responseData['message'] = $message;
        $this->responseData['success'] = true;
        $this->responseData['code'] = 0;
        $this->responseData['reqId'] = $this->getReqId();

        return $this->response->json($this->responseData);
    }

    public function apifail(string $message = '系统错误', int $code = 5000)
    {
        $this->responseData['message'] = $message;
        $this->responseData['code'] = $code;
        $this->responseData['success'] = false;
        $this->responseData['reqId'] = $this->getReqId();

        return $this->response->json($this->responseData);
    }

    public function apidata(string $message = '系统错误', int $code = 5000)
    {
        $this->responseData['message'] = $message;
        $this->responseData['code'] = $code;
        $this->responseData['success'] = false;
        $this->responseData['reqId'] = $this->getReqId();

        return $this->responseData;
    }

    public function setCode(int $code): static
    {
        $this->responseData['code'] = $code;
        return $this;
    }

    public function setMessage(string $message): static
    {
        $this->responseData['message'] = $message;
        return $this;
    }

    public function setData(?array $data): static
    {
        $this->responseData['data'] = $data;
        return $this;
    }
}
