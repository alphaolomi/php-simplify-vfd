<?php

use Alphaolomi\SimplifyVfd\SimplifyVfd;

it('can login with correct credentials', function () {

    $vfd = new SimplifyVfd([
        'enviroment' => 'stage',
    ]);
    $credentials = [
        'username' => getenv('VFD_USERNAME'),
        'password' => getenv('VFD_PASSWORD'),
    ];
    $loginResult = $vfd->userLogin($credentials);
    expect($loginResult)->toBeArray();
    expect($loginResult)->toHaveKeys(['token', 'refresh_token']);
})->skip(fn () => ! getenv('VFD_USERNAME') || ! getenv('VFD_PASSWORD'));

it('can createIssuedInvoice and fetch it', function () {

    $vfd = new SimplifyVfd([
        'enviroment' => 'stage',
    ]);
    $credentials = [
        'username' => getenv('VFD_USERNAME'),
        'password' => getenv('VFD_PASSWORD'),
    ];
    $loginResult = $vfd->userLogin($credentials);

    // print_r($loginResult);

    $body = [
        'dateTime' => date('Y-m-d'), //"2023-06-11",
        'customer' => [
            'identificationType' => 'TAX_IDENTIFICATION_NUMBER',
            'identificationNumber' => '123456789',
            'vatRegistrationNumber' => '400123456',
            'name' => 'John Doe',
            'mobileNumber' => '0999999999',
            'email' => 'user@example.com',
        ],
        'invoiceAmountType' => 'INCLUSIVE',
        'items' => [
            [
                'description' => 'Tests',
                'quantity' => 1,
                'unitAmount' => '100',
                'discountRate' => 0,
                'taxType' => 'STANDARD',
            ],
        ],
        'payments' => [
            [
                'type' => 'CASH',
                'amount' => '100',
            ],
        ],
        // "partnerInvoiceId" => "{{$guid}}"
    ];

    $invoiveResult = $vfd->createIssuedInvoice($body);

    // print_r($invoiveResult);
    // Array
    // (
    //     [success] => 1
    //     [invoiceId] => 884b4462-e122-4ce1-92aa-921b9366fcc2
    //     [verificationCode] => C34A8220253
    //     [verificationUrl] => https://virtual.tra.go.tz/efdmsRctVerify/C34A8220253_101657
    //     [issuedAt] => 2024-06-13 10:16:57
    // )

    expect($invoiveResult)->toBeArray();
    expect($invoiveResult)->toHaveKeys(['success', 'invoiceId', 'verificationCode', 'verificationUrl', 'issuedAt']);

    $invoiceId = $invoiveResult['invoiceId'];

    $invoiveResult = $vfd->getInvoiceByPartnerInvoiceId($invoiceId);

    // print_r($invoiveResult);

    expect($invoiveResult)->toBeArray();
})->skip(fn () => ! getenv('VFD_USERNAME') || ! getenv('VFD_PASSWORD'));

it('can\'t login with wrong credentials', function () {
    $vfd = new SimplifyVfd([
        'enviroment' => 'stage',
    ]);
    $loginResult = $vfd->userLogin([
        'username' => 'test@example.com',
        'password' => 'bad-password',
    ]);
    expect($loginResult)->toBeArray();

    // [401 , Invalid credentials]
    expect($loginResult)->toHaveKeys(['code', 'message']);
});
