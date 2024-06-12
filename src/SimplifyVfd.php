<?php

namespace Alphaolomi\SimplifyVfd;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;

class SimplifyVfd
{
    const LIVE_BASE_URI = 'https://simplify.co.tz/partner/v1/';

    const STAGE_BASE_URI = 'https://stage.simplify.co.tz/partner/v1/';

    private $httpClient;

    private $baseUri;

    private $token;

    private $configs = [];

    /**
     * @param  array  $configs{
     *
     * @var string
     * @var string
     * @var string
     *             }
     */
    public function __construct($configs)
    {
        $this->configs = $configs;

        // string $baseUri, string $token = null
        $this->baseUri = $configs['enviroment'] !== 'live' ? self::STAGE_BASE_URI : self::LIVE_BASE_URI;

        // $this->httpClient = new GuzzleClient(['base_uri' => $this->baseUri]);
        $this->httpClient = new GuzzleClient();

    }

    private function request(string $method, string $uri, array $options = [])
    {
        if ($this->token) {
            $options['headers']['Authorization'] = 'Bearer '.$this->token;
        }

        // try {
        $response = $this->httpClient->request($method, $uri, $options);

        return json_decode($response->getBody()->getContents(), true);
        // } catch (RequestException $e) {
        //     if ($e->hasResponse()) {
        //         throw new \Exception($e->getResponse()->getBody()->getContents());
        //     }
        //     throw new \Exception($e->getMessage());
        // }
    }

    // User Authentication Methods
    public function userLogin(array $data)
    {
        $base = 'https://stage.simplify.co.tz/partner/v1/auth/';

        $result = $this->request('POST', $base.'/user/login', ['json' => $data]);

        $this->token = $result['token'];

        return $result;
    }

    public function userRefreshToken(array $data)
    {
        $base = 'https://stage.simplify.co.tz/partner/v1/auth/';

        return $this->request('POST', $base.'/user/token/refresh', ['json' => $data]);
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

    // Datetime: it is the datetime when the invoice should be issued
    // ● Customer: it is the object with all information of customer
    // ○ identificationType: it is the type of identification of customer could be one of this
    // below:
    // ■ TAX_IDENTIFICATION NUMBER
    // ■ PASSPORT
    // ■ VOTERS_NUMBER
    // ■ DRIVING_LICENCE
    // ■ NATIONAL_IDENTIFICATION_AUTHORITY
    // ■ NO_IDENTIFICATION
    // ○ identificationNumber: it is the number associated with the type of identification of
    // a customer. This could not be mandatory when the type is
    // “NO_IDENTIFICATION”
    // ○ vatRegistrationNumber: it is the VAT Registration Number of customer that
    // should be applied when TAX_IDENTIFICATION_NUMBER is the type of
    // identification of customer.
    // ○ Name: it is the name of customer
    // ○ MobileNumber: it is the phone number of customer
    // ○ Email: it is the email address of a customer.
    // ● invoiceAmountType: it is the type of tax to apply in the amounts of items should the
    // below options:
    // ○ INCLUSIVE: it is when the amount has included the taxes.
    // ○ EXCLUSIVE: it is when the amount has excluded the taxes.
    // ● Items: this are the items to be included in the invoice
    // ○ Description: it is the name/description of product or services sold by the company
    // ○ Quantity: it is the quantity of the same item sold.
    // ○ unitAmount: it is the unit amount of item to be sold
    // ○ discountRate: it is the rate to be applied when the item has discount
    // ○ taxType: it is the type of taxes applied to the item to be sold. Could be the
    // following option
    // ■ STANDARD: it should be 18 %
    // ■ ZERO_RATED: it should be 0 %
    // ■ SPECIAL_RATE: it should be 0 %
    // ■ SPECIAL_RELIEF: it should be 0 %
    // ■ EXEMPTED: it should be 0 %
    // ● Payments: it should be the amounts and payment type applied to generate the receipt.
    // The sum of all payments should be equal to the total amount of invoice.
    // ○ Type: This must be the payment type used to pay off the invoice debt. The type
    // should be the following:
    // ■ CASH
    // ■ CCARD
    // ■ EMONEY
    // ■ CHEQUE
    // ■ INVOICE
    // ○ Amount: this is the amount pay off by payment type
    // ● partnerInvoiceId: it is the identification number of invoice generated in external system to
    // get the receipt using this field as criteria option in Simplify
    //
    // Array
    // (
    //     [success] => 1
    //     [invoiceId] => 7d35df50-f64e-40cb-b235-92b1c5a0ea76
    //     [verificationCode] => C34A8220251
    //     [verificationUrl] => https://virtual.tra.go.tz/efdmsRctVerify/C34A8220251_222629
    //     [issuedAt] => 2024-06-11 22:26:29
    // )
    public function createIssuedInvoice(array $data)
    {

        // {
        //     "dateTime": "2023-06-11",
        //     "customer": {
        //         "identificationType": "TAX_IDENTIFICATION_NUMBER",
        //         "identificationNumber": "123456789",
        //         "vatRegistrationNumber": "400123456",
        //         "name": "John Doe",
        //         "mobileNumber": "0999999999",
        //         "email": "user@example.com"
        //     },
        //     "invoiceAmountType": "INCLUSIVE",
        //     "items": [
        //         {
        //             "description": "Tests",
        //             "quantity": 1,
        //             "unitAmount": "100",
        //             "discountRate": 0,
        //             "taxType": "STANDARD"
        //         }
        //     ],
        //     "payments": [
        //         {
        //             "type": "CASH",
        //             "amount": "100"
        //         }
        //     ],
        //     "partnerInvoiceId": "{{$guid}}"
        // }

        $data = [
            'dateTime' => '2023-06-11',
            'customer' => [
                'identificationType' => 'TAX_IDENTIFICATION_NUMBER',
                'identificationNumber' => '123456789',
                'vatRegistrationNumber' => '400123456',
                'name' => 'John Doe',
                'mobileNumber' => '0999999999',
                'email' => '',
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
            'partnerInvoiceId' => $this->generateGuid(),
        ];

        // https://stage.simplify.co.tz/partner/v1/
        $base = 'https://stage.simplify.co.tz/partner/v1/';

        return $this->request('POST', $base.'/invoice/createIssuedInvoice', ['json' => $data]);
    }

    public function createIssuedInvoiceWithSerial(array $data)
    {
        return $this->request('POST', '/invoice/createIssuedInvoiceWithSerial', ['json' => $data]);
    }

    public function createBulkIssuedInvoices(array $data)
    {
        return $this->request('POST', '/invoice/createBulkIssuedInvoice', ['json' => $data]);
    }

    // generate guid
    public function generateGuid()
    {
        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 0xFFFF),
            mt_rand(0, 0xFFFF),
            mt_rand(0, 0xFFFF),
            mt_rand(0, 0x0FFF) | 0x4000,
            mt_rand(0, 0x3FFF) | 0x8000,
            mt_rand(0, 0xFFFF),
            mt_rand(0, 0xFFFF),
            mt_rand(0, 0xFFFF)
        );
    }
}
