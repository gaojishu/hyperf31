<?php

use App\Util\Auth\Auth;

test('example', function () {
    $token = Auth::guard(Auth::GUARD_ADMIN)->generateToken(1);
    echo $token;
    expect(true)->toBeTrue();
});
