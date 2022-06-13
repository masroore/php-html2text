# A PHP package to convert HTML into plain text -- no HTML tags allowed in the output.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/masroore/html2text.svg?style=flat-square)](https://packagist.org/packages/masroore/html2text)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/masroore/php-html2text/run-tests?label=tests)](https://github.com/masroore/php-html2text/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/masroore/php-html2text/Check%20&%20fix%20styling?label=code%20style)](https://github.com/masroore/php-html2text/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/masroore/html2text.svg?style=flat-square)](https://packagist.org/packages/masroore/html2text)

## Overview ##

**masroore/html2text** is a PHP package that converts a page of HTML into clean, easy-to-read plain ASCII text.

## Installation

> **Requires [PHP 8.0+](https://php.net/releases/)**

You can install the package via composer:

```bash
composer require masroore/html2text
```

## Usage

Extract text from HTML:

```php
use Kaiju\Html2Text\Html2Text;

$converter = new Html2Text();
echo $converter->convert($html);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Thank you for considering to contribute to Html2Text. All the contribution guidelines are mentioned [here](CONTRIBUTING.md).

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Masroor Ehsan](https://github.com/masroore)
- [All Contributors](../../contributors)

## License

Html2Text is an open-sourced software licensed under the [MIT license](LICENSE.md).
