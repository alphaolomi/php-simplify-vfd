<?php

use Alphaolomi\SimplifyVfd\SimplifyVfd;

it('can instantiate the class', function () {
    expect(true)->toBeTrue();

    $vfd = new SimplifyVfd([
        'enviroment' => 'stage',
    ]);

    expect($vfd)->toBeInstanceOf(SimplifyVfd::class);
});



it('can\'t login with wrong credentials', function () {
    expect(true)->toBeTrue();

    $vfd = new SimplifyVfd([
        'enviroment' => 'stage',
    ]);

    $loginResult = $vfd->userLogin([
        'username' => 'test',
        'password' => 'test',
    ]);

    expect($loginResult)->toBeArray();
    // other assertions
});



it('can login with correct credentials', function () {
    expect(true)->toBeTrue();

    $vfd = new SimplifyVfd([
        'enviroment' => 'stage',
    ]);

    // load .env file
    $envs = file_get_contents(__DIR__ . '/../.env');
    $envs = explode("\n", $envs);
    foreach ($envs as $env) {
        $env = trim($env); // Remove whitespace
        if ($env && strpos($env, '#') !== 0) { // Ignore empty lines and comments
            putenv($env);
        }
    }

    // get credentials from .env
    $creds = [
        'VFD_USERNAME' => getenv('VFD_USERNAME'),
        'VFD_PASSWORD' => getenv('VFD_PASSWORD'),
    ];
    $loginResult = $vfd->userLogin([
        'username' => $creds['VFD_USERNAME'],
        'password' => $creds['VFD_PASSWORD'],
    ]);

    expect($loginResult)->toBeArray();
    // other assertions
    // Return true if .env file doesn't exist
})->skip(fn () => !file_exists(__DIR__ . '/../.env'));
