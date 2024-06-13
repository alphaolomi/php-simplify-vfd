<?php

namespace Alphaolomi\SimplifyVfd;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class SimplifyVfd
{

    const PROD_BASE_URL = 'https://api.simplify.co.tz/partner/v1';
    const STAGE_BASE_URL = 'https://stage.simplify.co.tz/partner/v1';

    private $client;
    private $config;
    private $token = null;
    private $baseUrl = null;

    /**
     * SimplifyVfd constructor
     *
     * Environment can be either 'live' or 'stage'
     *
     * @param array $config{environment: string, username: string, password: string}
     * @param GuzzleClient $client
     *
     * @return void
     */
    public function __construct($config, $client = null)
    {
        // $this->config = $config;

        // is environment field is not set, default to stage
        if (!isset($config['environment'])) {
            $this->config['environment'] = 'stage';
        }

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];


        // set base url based on environment
        if ($this->config['environment'] == 'live') {
            $this->baseUrl = self::PROD_BASE_URL;
        } else {
            $this->baseUrl = self::STAGE_BASE_URL;
        }

        $this->client = $client ?? new GuzzleClient([
            'headers' => $headers,
            'http_errors' => false,
        ]);
    }


    /**
     * User Login
     *
     * @param array $data{username: string, password: string}
     *
     * @return array{token: string, refresh_token: string}
     */
    public function userLogin($data)
    {
        // check if username and password is set
        if (!isset($data['username']) || !isset($data['password'])) {
            throw new \Exception('Username and password are required');
        }

        $body = [
            'username' => $data['username'],
            'password' => $data['password']
        ];

        // $uri =  'https://stage.simplify.co.tz/partner/v1/auth/user/login';
        $uri =  $this->baseUrl . '/auth/user/login';

        $request = new Request('POST', $uri, [], json_encode($body));

        $resposne  = $this->client->sendAsync($request)->wait();


        $responseBody = json_decode($resposne->getBody()->getContents(), true);

        if ($resposne->getStatusCode() == 200) {
            $this->token = $responseBody['token'];
        }

        return $responseBody;
    }


    /**
     * Create Issued Invoice
     *
     * @param array $data
     *
     * @return array{success: int, invoiceId: string, verificationCode: string, verificationUrl: string, issuedAt: string}
     */
    public function createIssuedInvoice($data)
    {
        $body = [
            'dateTime' => $data['dateTime'],
            'customer' => [
                'identificationType' => $data['customer']['identificationType'],
                'identificationNumber' => $data['customer']['identificationNumber'],
                'vatRegistrationNumber' => $data['customer']['vatRegistrationNumber'],
                'name' => $data['customer']['name'],
                'mobileNumber' => $data['customer']['mobileNumber'],
                'email' => $data['customer']['email']
            ],
            'invoiceAmountType' => $data['invoiceAmountType'],
            'items' => $data['items'],
            'payments' => $data['payments'],
            'partnerInvoiceId' => $this->generateGuid()
        ];

        // $uri =  'https://stage.simplify.co.tz/partner/v1/invoice/createIssuedInvoice';
        $uri =  $this->baseUrl . '/invoice/createIssuedInvoice';

        $request = new Request('POST', $uri, [
            'Authorization' => 'Bearer ' . $this->token,
        ], json_encode($body));

        $resposne  = $this->client->sendAsync($request)->wait();

        return json_decode($resposne->getBody()->getContents(), true);
    }


    /**
     * Get Invoice By Partner Invoice Id
     *
     * @param string $partnerInvoiceId
     *
     * @return array
     */
    public function getInvoiceByPartnerInvoiceId($partnerInvoiceId)
    {
        // $uri =  'https://stage.simplify.co.tz/partner/v1/invoice/getInvoiceByPartnerInvoiceId/' . $partnerInvoiceId;
        $uri = sprintf('%s/invoice/getInvoiceByPartnerInvoiceId/%s', $this->baseUrl, $partnerInvoiceId);

        $request = new Request('GET', $uri, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $resposne  = $this->client->sendAsync($request)->wait();

        return json_decode($resposne->getBody()->getContents(), true);
    }

    /**
     * Generate GUID
     *
     * Note: This is a simple implementation of GUID generation
     *
     * @return string
     */
    private function generateGuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
}
