<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace HyperfTest\Cases;

use Hyperf\Testing\TestCase;
use HyperfTest\HttpTestCase;

/**
 * @internal
 * @coversNothing
 */
class AdminAuthTest extends HttpTestCase
{
    public function testInfo()
    {

        // 错误参数
        $response = $this->request('get', '/admin/auth/info');

        $body = $response->getBody()->getContents();

        $body = json_decode($body, true);

        var_dump($body);

        $this->assertSame(200, $response->getStatusCode());
    }
    public function testLoginPost()
    {
        // 错误参数
        $response = $this->request('post', '/admin/auth/login', [
            'json' => [
                'username' => '中文中文中文中文',
                'password' => '111111Aaa.',
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);


        $body = $response->getBody()->getContents();

        $body = json_decode($body, true);

        var_dump($body);

        //$this->assertSame(200, $response->getStatusCode());


        // 正常参数
        $response = $this->request('post', '/admin/auth/login', [
            'json' => [
                'username' => 'admin123',
                'password' => 'Aa123321.',
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);


        $body = $response->getBody()->getContents();

        $body = json_decode($body, true);

        var_dump($body);

        $this->assertSame(200, $response->getStatusCode());


        // 示例：断言特定字段存在
        // $this->assertArrayHasKey('token', $data['data']);
        // $this->assertIsString($data['data']['token']);

    }
}
