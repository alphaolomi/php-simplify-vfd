<?php

namespace Alphaolomi\SimplifyVfd;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class SimplifyVfd
{

    private $client;

    public function __construct($config)
    {

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        $this->client = new GuzzleClient([
            'headers' => $headers
        ]);
    }


    public function userLogin($data)
    {
        $body = [
            'username' => $data['username'],
            'password' => $data['password']
        ];

        $uri =  'https://stage.simplify.co.tz/partner/v1/auth/user/login';

        $request = new Request('POST', $uri, [], json_encode($body));

        $resposne  = $this->client->sendAsync($request)->wait();

        return json_decode($resposne->getBody()->getContents(), true);
    }
}
