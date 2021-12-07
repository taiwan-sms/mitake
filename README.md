# Mitake notifications channel for Laravel 5.3+

[![StyleCI](https://styleci.io/repos/83760327/shield?style=flat)](https://styleci.io/repos/83760327)
[![Build Status](https://travis-ci.org/taiwan-sms/mitake.svg)](https://travis-ci.org/taiwan-sms/mitake)
[![Total Downloads](https://poser.pugx.org/taiwan-sms/mitake/d/total.svg)](https://packagist.org/packages/taiwan-sms/mitake)
[![Latest Stable Version](https://poser.pugx.org/taiwan-sms/mitake/v/stable.svg)](https://packagist.org/packages/taiwan-sms/mitake)
[![Latest Unstable Version](https://poser.pugx.org/taiwan-sms/mitake/v/unstable.svg)](https://packagist.org/packages/taiwan-sms/mitake)
[![License](https://poser.pugx.org/taiwan-sms/mitake/license.svg)](https://packagist.org/packages/taiwan-sms/mitake)
[![Monthly Downloads](https://poser.pugx.org/taiwan-sms/mitake/d/monthly)](https://packagist.org/packages/taiwan-sms/mitake)
[![Daily Downloads](https://poser.pugx.org/taiwan-sms/mitake/d/daily)](https://packagist.org/packages/taiwan-sms/mitake)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/taiwan-sms/mitake/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/taiwan-sms/mitake/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/taiwan-sms/mitake/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/taiwan-sms/mitake/?branch=master)

This package makes it easy to send notifications using [mitake] with Laravel 5.3+.

## Contents

- [Installation](#installation)
    - [Setting up the Mitake service](#setting-up-the-Mitake-service)
- [Usage](#usage)
    - [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

## Installation

You can install the package via composer:

```bash
composer require taiwan-sms/mitake illuminate/notifications php-http/guzzle6-adapter
```

Then you must install the service provider:

```php
// config/app.php
'providers' => [
    ...
    TaiwanSms\Mitake\MitakeServiceProvider::class,
],
```

### Setting up the Mitake service

Add your Mitake login, secret key (hashed password) and default sender name (or phone number) to
your `config/services.php`:

```php
// config/services.php
...
'mitake' => [
    'username' => env('SERVICES_MITAKE_USERNAME'),
    'password' => env('SERVICES_MITAKE_PASSWORD'),
],
...
```

## Usage

You can use the channel in your `via()` method inside the notification:

```php
use TaiwanSms\Mitake\MitakeMessage;
use TaiwanSms\Mitake\MitakeChannel;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [MitakeChannel::class];
    }

    public function toMitake($notifiable)
    {
        return MitakeMessage::create("Task #{$notifiable->id} is complete!");
    }
}
```

In your notifiable model, make sure to include a routeNotificationForTwSMS() method, which return the phone number.

```php
public function routeNotificationForTwSMS()
{
    return $this->phone;
}
```

### Available methods

`subject()`: Sets a subject of the notification subject.

`content()`: Sets a content of the notification message.

`sendTime()`: Set send time of the notification message.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email recca0120@gmail.com instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [JhaoDa](https://github.com/recca0120)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

# API Only

```bash
composer require taiwan-sms/mitake php-http/guzzle6-adapter
```

## How to use

```php
require __DIR__.'/vendor/autoload.php';

use TaiwanSms\Mitake\Client;

$userId = 'xxx';
$password = 'xxx';

$client = new Client($userId, $password);

var_dump($client->credit()); // 取得額度
var_dump($client->send([
    'to' => '09xxxxxxxx',
    'text' => 'test message',
]));
/*
return [
    'msgid' => '0892448417',
    'statuscode' => '1',
    'AccountPoint' => '97',
];
*/
```
