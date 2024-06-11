# Simplify VFD for PHP

[![Latest Version on Packagist](https://img.shields.io/packagist/v/alphaolomi/simplify-vfd.svg?style=flat-square)](https://packagist.org/packages/alphaolomi/simplify-vfd)
[![Tests](https://img.shields.io/github/actions/workflow/status/alphaolomi/simplify-vfd/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/alphaolomi/simplify-vfd/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/alphaolomi/simplify-vfd.svg?style=flat-square)](https://packagist.org/packages/alphaolomi/simplify-vfd)

This is where your description should go. Try and limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require alphaolomi/simplify-vfd
```

## Usage

```php
$config = [
    'username' => 'your_username',
    'password' => 'your_password'
];
$service = new Alphaolomi\SimplifyVfd($config);

$data = [
    'data' => 'data'
];

$result  = $service->createInvoice($data);

print_r($result);
```

```json
{
    "success": true,
    "invoiceId": "161c2435-d321-4ac1-81d5-67b0563b9528",
    "verificationCode": "C34A8220250",
    "verificationUrl": "https://virtual.tra.go.tz/efdmsRctVerify/C34A8220250_164730",
    "issuedAt": "2024-06-11 16:47:30"
}
```

## Testing

Using PestPHP for testing

Code coverage is generated by PHPUnit. Codecov is used to generate code coverage reports.

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Alpha Olomi](https://github.com/alphaolomi)
- [Ernest Malcolm](httds://github.com/ernest)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.