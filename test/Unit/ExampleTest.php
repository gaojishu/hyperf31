<?php

use App\Util\Auth\Auth;

test('example', function () {
    $token = Auth::guard(Auth::GUARD_ADMIN)->generateToken(1);
    var_dump($token);
    expect(true)->toBeTrue();
});
