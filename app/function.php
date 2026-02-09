<?php

use Hyperf\Context\ApplicationContext;
use Hyperf\HttpServer\Contract\RequestInterface;

if (! function_exists('get_request_token')) {
    function get_request_token(): ?string
    {
        $request = ApplicationContext::getContainer()->get(RequestInterface::class);
        $auth = $request->getHeaderLine('Authorization');
        if (str_starts_with($auth, 'Bearer ')) {
            return substr($auth, 7);
        }
        return $request->getQueryParams()['token'] ?? null;
    }
}


//传入字符串，自动识别具体类型
if (! function_exists('parse_value')) {
    function parseValue(string $value): int|float|bool|array|string
    {
        // 1. 尝试识别布尔值 (处理 "true", "false", "on", "off")
        $boolean = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        if ($boolean !== null) return $boolean;

        // 2. 尝试识别整数
        if (filter_var($value, FILTER_VALIDATE_INT) !== false) return (int)$value;

        // 3. 尝试识别浮点数
        if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) return (float)$value;

        // 4. 尝试识别 JSON (针对复杂配置)
        $json = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE) return $json;

        // 5. 默认作为字符串返回
        return $value;
    }
}
