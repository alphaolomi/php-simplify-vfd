# Changelog

All notable changes to `simplify-vfd` will be documented in this file.

## v0.1.0 - 2024-06-13

### :tada: Release Notes for v0.1.0

#### Overview

I'm excited to announce the first release of the Simplify VFD PHP library, version 0.1.0. This initial release provides a robust set of features to interact with the Simplify VFD API, enabling seamless integration of VFD services into your PHP applications.

#### Features

##### User Authentication

- **Login**: Authenticate users and obtain access tokens using their Simplify VFD credentials.
- **Token Management**: Store and manage authentication tokens for making secure API requests.

##### Invoice Management

- **Create Issued Invoice**: Generate and issue invoices with detailed customer and payment information.
- **Retrieve Invoice**: Fetch invoice details using a unique partner invoice ID.

##### Environment Configuration

- **Live and Stage Environments**: Easily switch between live and stage environments using configuration settings.

##### HTTP Client Integration

- **Guzzle HTTP Client**: Utilizes Guzzle for making API requests, ensuring reliability and ease of use.

##### Utility Functions

- **GUID Generation**: Simple implementation for generating unique identifiers (GUIDs) for invoices and other entities.

#### Installation

You can install the package via Composer:

```bash
composer require alphaolomi/simplify-vfd

```
#### Usage

##### Initializing the Service

```php
use Alphaolomi\SimplifyVfd\SimplifyVfd;

$config = [
    'environment' => 'stage', // or 'live'
    'username' => 'your_username',
    'password' => 'your_password',
];

$service = new SimplifyVfd($config);

```
##### User Login

```php
$data = [
    'username' => 'your_username',
    'password' => 'your_password'
];

$response = $service->userLogin($data);
print_r($response);

```
##### Create Issued Invoice

```php
$data = [
    'dateTime' => '2024-06-11T16:47:30',
    'customer' => [
        'identificationType' => 'ID',
        'identificationNumber' => '1234567890',
        'vatRegistrationNumber' => '123456789',
        'name' => 'Customer Name',
        'mobileNumber' => '255123456789',
        'email' => 'customer@example.com',
    ],
    'invoiceAmountType' => 'GROSS',
    'items' => [
        [
            'itemName' => 'Product 1',
            'quantity' => 1,
            'price' => 100.00
        ]
    ],
    'payments' => [
        [
            'paymentType' => 'CASH',
            'amount' => 100.00
        ]
    ],
    'partnerInvoiceId' => 'unique-invoice-id'
];

$response = $service->createIssuedInvoice($data);
print_r($response);

```
##### Get Invoice by Partner Invoice ID

```php
$partnerInvoiceId = 'unique-invoice-id';

$response = $service->getInvoiceByPartnerInvoiceId($partnerInvoiceId);
print_r($response);

```
#### Bug Fixes and Improvements

- Initial implementation with core functionalities.
- Stable integration with Simplify VFD API endpoints.
- Comprehensive error handling and validation for API requests.

#### Known Issues

- None reported for this release.

#### Future Improvements

- Add support for more API endpoints as they become available.
- Implement more comprehensive logging and debugging features.
- Enhance test coverage and continuous integration setup.

#### Contributing

We welcome contributions from the community. Please see the [CONTRIBUTING](https://github.com/alphaolomi/php-simplify-vfd/blob/main/CONTRIBUTING.md) guide for more details.

#### License

This project is licensed under the MIT License. See the [LICENSE](https://github.com/alphaolomi/php-simplify-vfd/blob/main/LICENSE.md) file for more information.

**Full Changelog**: https://github.com/alphaolomi/php-simplify-vfd/compare/v0.0.1-beta...v0.1.0

## 0.1.0 - 2024-06-13

## Features

- **User Authentication**: Authenticate users and obtain access tokens.
- **Invoice Management**: Create, retrieve, and manage invoices.
- **Environment Configuration**: Switch between live and stage environments.
- **HTTP Client Integration**: Uses Guzzle HTTP client for making API requests.
- **Custom GUID Generation**: Utility function for generating unique identifiers.
