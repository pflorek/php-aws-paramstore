# PHP AWS paramstore

[![Build Status](https://travis-ci.org/pflorek/php-aws-paramstore.svg?branch=master)](https://travis-ci.org/pflorek/php-aws-paramstore)
[![Coverage Status](https://coveralls.io/repos/github/pflorek/php-aws-paramstore/badge.svg?branch=master)](https://coveralls.io/github/pflorek/php-aws-paramstore?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/pflorek/php-aws-paramstore/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/pflorek/php-aws-paramstore/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/pflorek/aws-paramstore/v/stable)](https://packagist.org/packages/pflorek/aws-paramstore)
[![Total Downloads](https://poser.pugx.org/pflorek/aws-paramstore/downloads)](https://packagist.org/packages/pflorek/aws-paramstore)
[![Latest Unstable Version](https://poser.pugx.org/pflorek/aws-paramstore/v/unstable)](https://packagist.org/packages/pflorek/aws-paramstore)
[![License](https://poser.pugx.org/pflorek/aws-paramstore/license)](https://packagist.org/packages/pflorek/aws-paramstore)
[![Monthly Downloads](https://poser.pugx.org/pflorek/aws-paramstore/d/monthly)](https://packagist.org/packages/pflorek/aws-paramstore)
[![Daily Downloads](https://poser.pugx.org/pflorek/aws-paramstore/d/daily)](https://packagist.org/packages/pflorek/aws-paramstore)
[![composer.lock](https://poser.pugx.org/pflorek/aws-paramstore/composerlock)](https://packagist.org/packages/pflorek/aws-paramstore)


## Usage

```PHP
use Aws\Ssm\SsmClient;
use \PFlorek\AwsParameterStore\ConfigProvider;

// Provide bootstrap options
$options = [
    'prefix' => '/path/with/prefix', // required
    'name' => 'application-name', // required
    'profileSeparator' => '_', // default => '_'
    'sharedContext' => 'shared-context', // default => ''
];

// Configure AWS Systems Manager Client
$client = new SsmClient([]);

// Create AWS Parameter Store Config Provider
$provider = ConfigProvider::create($client, $options);

// Optionally get provided config with profiles
$activeProfiles = ['test'];
$config = $provider->provide($activeProfiles);

//returns
//
//array(1) {
//  ["service"]=>
//  array(3) {
//    ["host"]=>
//    string(5) "mysql"
//    ["port"]=>
//    int(3306)
//    ["enabled"]=>
//    bool(true)
//  }
//}
```

## Configuration

| parameter | required | default | description |
| :--- | :---: | :---: | :--- |
| prefix | _yes_ | _none_ | The path prefix of the parameters |
| name | _yes_ | _none_ | The application name |
| profileSeparator | _no_ | **'_'** | The separator between application name and profile |
| sharedContext | _no_ | **''** | The shared context for application with parameters under the same path prefix |

## Example

Given options:

- prefix := **/path/with/prefix**
- name := **app-name**
- profileSeparator := **_**
- sharedContext = **shared**
- profiles = **['common', 'test']**

Will search for parameters with path beginning with:

1. /path/with/prefix/shared
1. /path/with/prefix/app-name
1. /path/with/prefix/app-name_common
1. /path/with/prefix/app-name_test

## Installation

Use [Composer] to install the package:

```bash
composer require pflorek/aws-paramstore
```

## Authors

* [Patrick Florek]

## Contribute

Contributions are always welcome!

* Report any bugs or issues on the [issue tracker].
* You can download the sources at the package's [Git repository].

## License

All contents of this package are licensed under the [MIT license].

[Composer]: https://getcomposer.org
[Git repository]: https://github.com/pflorek/php-aws-paramstore
[issue tracker]: https://github.com/pflorek/php-aws-paramstore/issues
[MIT license]: LICENSE
[Patrick Florek]: https://github.com/pflorek
