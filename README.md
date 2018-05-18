# Dhiraagu SMS

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dash8x/dhiraagu-sms.svg?style=flat-square)](https://packagist.org/packages/dash8x/dhiraagu-sms)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/dash8x/dhiraagu-sms/master.svg?style=flat-square)](https://travis-ci.org/dash8x/dhiraagu-sms)
[![StyleCI](https://styleci.io/repos/:style_ci_id/shield)](https://styleci.io/repos/:style_ci_id)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/:sensio_labs_id.svg?style=flat-square)](https://insight.sensiolabs.com/projects/:sensio_labs_id)
[![Quality Score](https://img.shields.io/scrutinizer/g/dash8x/dhiraagu-sms.svg?style=flat-square)](https://scrutinizer-ci.com/g/dash8x/dhiraagu-sms)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/dash8x/dhiraagu-sms/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/dash8x/dhiraagu-sms/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/dash8x/dhiraagu-sms.svg?style=flat-square)](https://packagist.org/packages/dash8x/dhiraagu-sms)

PHP SDK for [Dhiraagu Bulk SMS Gateway](https://bulkmessage.dhiraagu.com.mv)

## Installation
You can install **dhiraagu-sms** via composer or by downloading the source.

**Via Composer:**

```bash
composer require dash8x/dhiraagu-sms
```

## Quickstart

### Send an SMS

```php
// Send an SMS using Dhiraagu Bulk SMS Gateway and PHP
<?php
$username = "XXXXXXX"; // The username that you received from Dhiraagu (usually same as your SMS sender name)
$password = "YYYYYY"; // Your Dhiraagu Bulk SMS Gateway password

$client = new \Dash8x\DhiraaguSms\DhiraaguSms($username, $password);
$message = $client->send(
  '+9607777777', // Text this number, use an array to send to multiple numbers
  'Hello World!' // Your message
);

print $message->message_id;
```

### Check SMS Delivery Status

To check the delivery status of an SMS, first you will need to obtain the **`message_key`** and **`message_id`** when you send the SMS.

```php
// Check the delivery status of an SMS using Dhiraagu Bulk SMS Gateway and PHP
<?php
$username = "XXXXXXX"; // The username that you received from Dhiraagu (usually same as your SMS sender name)
$password = "YYYYYY"; // Your Dhiraagu Bulk SMS Gateway password

$client = new \Dash8x\DhiraaguSms\DhiraaguSms($username, $password);
$message = $client->send(
  '+9607777777', // Text this number, use an array to send to multiple numbers
  'Hello World!' // Your message
);

$delivery = $client->delivery(
  $message->message_id, // Message id
  $message->message_key // Message key
);

print $delivery->message_status_desc;

// Check the status for a particular recipient
$device = $delivery->getDevice('9607777777'); // Omit the + of the country code
print $device->status_desc;
```

## Demo
A demo implementation of this package can be found [here](https://github.com/dash8x/dhiraagu-sms-demo).

## Credits

- [Arushad Ahmed (@dash8x)](http://arushad.org)
- Extended from [tallminds/dhisms](https://packagist.org/packages/tallminds/dhisms)

## Disclaimer

This package is not in any way officially affiliated with Dhiraagu.
The "Dhiraagu" name has been used under fair use.

## License

This Dhiraagu Bulk SMS Gateway SDK is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
