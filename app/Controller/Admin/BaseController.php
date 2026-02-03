<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use Hyperf\Context\Context;
use Ramsey\Uuid\Uuid;

class BaseController extends AbstractController
{

    private $responseData = [
        'code' => 500,
        'httpStatus' => 'fail',
        'message' => 'fail',
        'success' => false,
        'reqId' => '',
        'data' => null,
    ];

    public function admin_id()
    {
        return Context::get('user_id');
    }

    public function apisucceed($message = '操作成功')
    {
        $this->responseData['message'] = $message;
        $this->responseData['success'] = true;
        $this->responseData['code'] = 0;
        $this->responseData['reqId'] = Uuid::uuid4()->toString();

        return $this->response->json($this->responseData);
    }


    public function apifail($message = '', $code = 5000)
    {
        $this->responseData['message'] = $message;
        $this->responseData['code'] = $code;
        $this->responseData['success'] = false;
        $this->responseData['reqId'] = Uuid::uuid4()->toString();

        return $this->response->json($this->responseData);
    }

    public function setCode($code = 0)
    {
        $this->responseData['code'] = $code;
        return $this;
    }

    public function setMessage($message = '操作成功')
    {
        $this->responseData['message'] = $message;
        return $this;
    }

    public function setData($data = null)
    {
        $this->responseData['data'] = $data;
        return $this;
    }
}
