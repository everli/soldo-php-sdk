# Soldo SDK for PHP

The following examples demonstrate how you would accomplish tasks with the Soldo SDK for PHP.

## Adding a Logger

You can pass a PSR-3 compliant Logger to the `Soldo` constructor to enable the logging capabilities that came out of the box.

Supposing you are using [Monolog](https://github.com/Seldaek/monolog)

```php
require_once __DIR__ . '/vendor/autoload.php';

$logger = new \Monolog\Logger('soldo');
$logger->pushHandler(new \Monolog\Handler\StreamHandler(__DIR__ . '/soldo.log', \Monolog\Logger::INFO));

$soldo = new \Soldo\Soldo([
    'client_id' => 'Eu97aMWTV3ta9AchozCozGn15XiX6t5x',
    'client_secret' => 'msNE5I1BnSkWBHPVRJDMYqKvTKRfCS4a',
], $logger);
```



## Going live

All the calls made currently are defaulted to sandbox (aka `demo`) environment. To go live simply add this

```php
$soldo = new \Soldo\Soldo([
    'client_id' => 'Eu97aMWTV3ta9AchozCozGn15XiX6t5x',
    'client_secret' => 'msNE5I1BnSkWBHPVRJDMYqKvTKRfCS4a',
    'environment => 'live'
], $logger);
```
