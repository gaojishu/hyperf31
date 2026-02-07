<?php

namespace App\Util\HttpResponse;

use App\Util\Auth\Auth;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

trait HttpResponse
{

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
        return Auth::guard(Auth::GUARD_ADMIN)->getUserId();
    }

    public function apisucceed(string $message = '')
    {

        $res = HttpResponseData::getResponseContext();
        $res['code'] = 0;
        $res['success'] = true;
        $res['message'] = $message;
        $res['httpStatus'] = 'ok';

        return $this->response->json($res);
    }

    public function apifail(string $message = '系统错误', int $code = 500)
    {
        $res = HttpResponseData::getResponseContext();
        $res['code'] = $code;
        $res['message'] = $message;
        $res['success'] = false;
        $res['httpStatus'] = 'fail';

        return $this->response->json($res)->withStatus($code);
    }


    public function setCode(int $code)
    {
        $res = HttpResponseData::getResponseContext();
        $res['code'] = $code;
        HttpResponseData::setResponseContext($res);

        return $this;
    }

    public function setMessage(string $message)
    {
        $res = HttpResponseData::getResponseContext();
        $res['message'] = $message;
        HttpResponseData::setResponseContext($res);
        return $this;
    }

    public function setData(mixed $data)
    {
        $res = HttpResponseData::getResponseContext();
        $res['data'] = $data;
        HttpResponseData::setResponseContext($res);
        return $this;
    }
}
