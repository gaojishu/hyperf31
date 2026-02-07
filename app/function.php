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
