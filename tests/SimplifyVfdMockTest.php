<?php

use Alphaolomi\SimplifyVfd\SimplifyVfd;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

it('can login with correct credentials', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode(['token' => 'dummy_token', 'refresh_token' => 'dummy_refresh_token'])),
    ]);
    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $vfd = new SimplifyVfd(['enviroment' => 'stage'], $client);

    $credentials = ['username' => 'username@server.tld', 'password' => 'secret-password'];

    $loginResult = $vfd->userLogin($credentials);

    // print_r($loginResult);
    // expect(true)->tobe(true);
    expect($loginResult)->toBeArray();
    expect($loginResult)->toHaveKeys(['token', 'refresh_token']);
});

it('cant login with wrong credentials', function () {
    $mock = new MockHandler([
        new Response(401, [], json_encode(['code' => 401, 'message' => 'Invalid credentials'])),
    ]);
    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack, 'http_errors' => false]);

    $vfd = new SimplifyVfd(['enviroment' => 'stage'], $client);

    $credentials = ['username' => 'username@server.tld', 'password' => 'secret-password'];

    $loginResult = $vfd->userLogin($credentials);

    expect($loginResult)->toBeArray();
    expect($loginResult)->toHaveKeys(['code', 'message']);
});

it('can createIssuedInvoice', function () {
    $mock = new MockHandler([
        new Response(200, [], json_encode([
            'token' => 'dummy_token',
            'refresh_token' => 'dummy_refresh_token',
        ])),
        new Response(200, [], json_encode([
            'success' => 1,
            'invoiceId' => 'dummy_invoice_id',
            'verificationCode' => 'dummy_verification_code',
            'verificationUrl' => 'dummy_verification_url',
            'issuedAt' => '2024-06-13 10:16:57',
        ])),
    ]);
    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack, 'http_errors' => false]);

    $vfd = new SimplifyVfd(['enviroment' => 'stage'], $client);

    $credentials = ['username' => 'username@server.tld', 'password' => 'secret-password'];
    $vfd->userLogin($credentials);

    $body = [
        'dateTime' => date('Y-m-d'),
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
    ];

    $invoiceResult = $vfd->createIssuedInvoice($body);

    expect($invoiceResult)->toBeArray();
    expect($invoiceResult)->toHaveKeys(['success', 'invoiceId', 'verificationCode', 'verificationUrl', 'issuedAt']);
});

it('can getInvoiceByPartnerInvoiceId', function () {

    $mock = new MockHandler([
        new Response(200, [], json_encode([
            'token' => 'dummy_token',
            'refresh_token' => 'dummy_refresh_token',
        ])),
        new Response(200, [], json_encode([
            'id' => '3fa85f64-5717-4562-b3fc-2c963f66afa6',
            'tin' => '123456',
            'customer_id_type' => 'TAX_IDENTIFICATION_NUMBER',
            'customer_id' => '123456789',
            'customer_vrn' => 'string',
            'customer_mobile_number' => '124785963',
            'customer_email' => 'jhon.doe@example.com',
            'customer_name' => 'Customer Name',
            'discount' => 145.29,
            'invoice_amount_type' => 'EXCLUSIVE',
            'receipt_number' => 1,
            'receipt_verification_code' => 'ABC7845',
            'status' => 'ISSUED',
            'total_tax_excluding' => 145.29,
            'total_tax_including' => 145.29,
            'created_at' => '2021-01-31 00:01:00',
            'issued_at' => '2021-01-31 00:01:00',
            'daily_counter' => '1',
            'z_number' => '20210131',
            'partner_invoice_id' => 'abcd-124',
            'items' => [
                [
                    'description' => 'string',
                    'quantity' => 1,
                    'unitAmount' => '145.29',
                    'discountRate' => 0,
                    'taxType' => 'STANDARD',
                    'fixedDiscount' => '0.00',
                ],
            ],
            'vat_totals' => [
                [
                    'vat_rate' => 'ZERO_RATED',
                    'netAmount' => 80.29,
                    'taxAmount' => 80.29,
                    'taxGroup' => 'C',
                ],
            ],
            'payments' => [
                [
                    'type' => 'CASH',
                    'amount' => '145.29',
                ],
            ],
        ])),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack]);

    $vfd = new SimplifyVfd(['enviroment' => 'stage'], $client);

    $credentials = ['username' => 'username@server.tld', 'password' => 'secret-password'];

    $vfd->userLogin($credentials);

    $invoiceId = 'abcd-124';

    $invoiceResult = $vfd->getInvoiceByPartnerInvoiceId($invoiceId);

    expect($invoiceResult)->toBeArray();

    $keys = [
        'id', 'tin', 'customer_id_type', 'customer_id', 'customer_vrn',
        'customer_mobile_number', 'customer_email', 'customer_name', 'discount', 'invoice_amount_type',
        'receipt_number', 'receipt_verification_code', 'status', 'total_tax_excluding', 'total_tax_including',
        'created_at', 'issued_at', 'daily_counter', 'z_number', 'partner_invoice_id', 'items', 'vat_totals', 'payments',
    ];

    expect($invoiceResult)->toHaveKeys($keys);
});

it('can\'t getInvoiceByPartnerInvoiceId with expired token', function () {

    // {"code": 401, "message": "Expired JWT Token"}
    $mock = new MockHandler([
        new Response(401, [], json_encode(['code' => 401, 'message' => 'Expired JWT Token'])),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack, 'http_errors' => false]);

    $vfd = new SimplifyVfd(['enviroment' => 'stage'], $client);

    $credentials = ['username' => 'username@server.tld', 'password' => 'secret-password'];

    // $vfd->userLogin($credentials);

    $invoiceId = 'abcd-124';

    $invoiceResult = $vfd->getInvoiceByPartnerInvoiceId($invoiceId);

    expect($invoiceResult)->toBeArray();
    expect($invoiceResult)->toHaveKeys(['code', 'message']);
});

it('can\'t getInvoiceByPartnerInvoiceId with 404 invoice', function () {

    $mock = new MockHandler([
        new Response(200, [], json_encode([
            'token' => 'dummy_token',
            'refresh_token' => 'dummy_refresh_token',
        ])),
        new Response(404, [], json_encode([
            'errorCode' => 'INVOICE_NOT_FOUND',
            'title' => 'Not Found',
            'message' => 'Please try again. If the problem persists, please contact our customer support.',
            'retriable' => false,
        ])),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $client = new Client(['handler' => $handlerStack, 'http_errors' => false]);

    $vfd = new SimplifyVfd(['enviroment' => 'stage'], $client);

    $credentials = ['username' => 'username@server.tld', 'password' => 'secret-password'];

    $vfd->userLogin($credentials);

    $invoiceId = 'missing-invoice-id';

    $invoiceResult = $vfd->getInvoiceByPartnerInvoiceId($invoiceId);

    expect($invoiceResult)->toBeArray();

    expect($invoiceResult)->toHaveKeys(['errorCode', 'title', 'message', 'retriable']);
});
