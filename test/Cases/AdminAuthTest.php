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

/**
 * @internal
 * @coversNothing
 */
class AdminAuthTest extends TestCase
{
    public function testLoginPost()
    {
        // 错误参数
        $response = $this->post('/admin/auth/login', [
            'username' => '中文中文中文中文',
            'password' => '111111Aaa.',
        ]);


        var_dump('http code:' . $response->getStatusCode());
        // 获取返回的 JSON 数据（自动 decode 为数组）
        $data = $response->json();

        var_dump('http body:');
        // 示例：打印整个响应
        var_dump($data);

        // 断言响应是否成功（可选）
        $response->assertOk(); // 等价于 assertStatus(200)

        // 正常参数
        $response = $this->post('/admin/auth/login', [
            'username' => 'admin123',
            'password' => 'Aa123321.',
        ]);


        var_dump('http code:' . $response->getStatusCode());
        // 获取返回的 JSON 数据（自动 decode 为数组）
        $data = $response->json();

        var_dump('http body:');
        // 示例：打印整个响应
        var_dump($data);

        // 示例：断言特定字段存在
        // $this->assertArrayHasKey('token', $data['data']);
        // $this->assertIsString($data['data']['token']);

        // 或者直接使用 assertJson() 断言结构
        // $response->assertJson([
        //     'code' => 200,
        //     'message' => '登录成功',
        // ]);

        // 如果你想获取原始字符串（非 JSON）
        // $content = $response->getContent();


        // 断言响应是否成功（可选）
        $response->assertOk(); // 等价于 assertStatus(200)

    }
}
