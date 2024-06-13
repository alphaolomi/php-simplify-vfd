<?php

use Alphaolomi\SimplifyVfd\SimplifyVfd;



it('can login with correct credentials', function () {

    $vfd = new SimplifyVfd([
        'enviroment' => 'stage',
    ]);
    $credentials = [
        'username' => "goog email here",
        'password' => "you nice password"
    ];
    $loginResult = $vfd->userLogin($credentials);
    expect($loginResult)->toBeArray();
    expect($loginResult)->toHaveKeys(['token', 'refresh_token']);
})->skip();


it('can\'t login with wrong credentials', function () {
    $vfd = new SimplifyVfd([
        'enviroment' => 'stage',
    ]);
    $loginResult = $vfd->userLogin([
        'username' => "test@example.com",
        'password' => "bad-password",
    ]);
    expect($loginResult)->toBeArray();
    // [401 , Invalid credentials]
    expect($loginResult)->toHaveKeys(['code', 'message']);
});
