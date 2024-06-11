<?php

namespace Alphaolomi\SimplifyVfd;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

class SimplifyVfd
{
    private $httpClient;
    private $baseUri;
    private $token;

    public function __construct(string $baseUri, string $token = null)
    {
        $this->baseUri = $baseUri;
        $this->httpClient = new GuzzleClient(['base_uri' => $this->baseUri]);
        $this->token = $token;
    }

    private function request(string $method, string $uri, array $options = [])
    {
        if ($this->token) {
            $options['headers']['Authorization'] = 'Bearer ' . $this->token;
        }

        try {
            $response = $this->httpClient->request($method, $uri, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                throw new \Exception($e->getResponse()->getBody()->getContents());
            }
            throw new \Exception($e->getMessage());
        }
    }

    // User Authentication Methods
    public function userLogin(array $data)
    {
        return $this->request('POST', '/user/login', ['json' => $data]);
    }

    public function userRefreshToken(array $data)
    {
        return $this->request('POST', '/user/token/refresh', ['json' => $data]);
    }

    // Invoice Methods
    public function getInvoiceByPartnerInvoiceId(string $partnerInvoiceId)
    {
        return $this->request('GET', "/invoice/getInvoiceByPartnerInvoiceId/{$partnerInvoiceId}");
    }

    public function createDraftInvoice(array $data)
    {
        return $this->request('POST', '/invoice/createDraftInvoice', ['json' => $data]);
    }

    public function createIssuedInvoice(array $data)
    {
        return $this->request('POST', '/invoice/createIssuedInvoice', ['json' => $data]);
    }

    public function createIssuedInvoiceWithSerial(array $data)
    {
        return $this->request('POST', '/invoice/createIssuedInvoiceWithSerial', ['json' => $data]);
    }

    public function createBulkIssuedInvoices(array $data)
    {
        return $this->request('POST', '/invoice/createBulkIssuedInvoice', ['json' => $data]);
    }
}
