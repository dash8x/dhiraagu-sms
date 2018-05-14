# Dhiraagu SMS
PHP SDK for [Dhiraagu Bulk SMS Gateway](https://bulkmessage.dhiraagu.com.mv)

## Installation
You can install **dhiraagu-sms** via composer or by downloading the source.

**Via Composer:**

```
composer install dash8x/dhiraagu-sms
```

## Quickstart

### Send an SMS

```php
// Send an SMS using Dhiraagu Bulk SMS Gateway and PHP
<?php
$username = "XXXXXXX"; // The username that you received from Dhiraagu (usually same as your SMS sender name)
$password = "YYYYYY"; // Your Dhiraagu Bulk SMS Gateway password

$client = new Dash8x\DhiraaguSms($username, $password);
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

$client = new Dash8x\DhiraaguSms($username, $password);
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
