<?php

namespace App\Util\HttpResponse;

use App\Util\Auth\Auth;
use Hyperf\Context\Context;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Ramsey\Uuid\Uuid;

trait HttpResponse
{

    private string $contextKey = 'http_response_data';

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


    private function getResponseContext(): array
    {
        return Context::getOrSet($this->contextKey, [
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
    private function setResponseContext(array $data): void
    {
        Context::set($this->contextKey, $data);
    }


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

        $res = $this->getResponseContext();
        $res['code'] = 0;
        $res['success'] = true;
        $res['message'] = $message;
        $res['httpStatus'] = 'ok';

        return $this->response->json($res);
    }

    public function apifail(string $message = '系统错误', int $code = 500)
    {
        $res = $this->getResponseContext();
        $res['code'] = $code;
        $res['message'] = $message;
        $res['success'] = false;
        $res['httpStatus'] = 'fail';

        return $this->response->json($res)->withStatus($code);
    }

    public function apidata(string $message = '系统错误', int $code = 500)
    {

        $res = $this->getResponseContext();
        $res['code'] = $code;
        $res['message'] = $message;
        $res['success'] = false;
        $res['httpStatus'] = 'fail';

        return $res;
    }

    public function setCode(int $code)
    {
        $res = $this->getResponseContext();
        $res['code'] = $code;
        $this->setResponseContext($res);

        return $this;
    }

    public function setMessage(string $message)
    {
        $res = $this->getResponseContext();
        $res['message'] = $message;
        $this->setResponseContext($res);
        return $this;
    }

    public function setData(mixed $data)
    {
        $res = $this->getResponseContext();
        $res['data'] = $data;
        $this->setResponseContext($res);
        return $this;
    }
}
