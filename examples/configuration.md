# Configuration

The following examples demonstrate how you would add logging capabilities to your `Soldo` object and what is the configuration required for going live.

## Adding a Logger

You can pass a PSR-3 compliant Logger to the `Soldo` constructor to enable the logging capabilities that came out of the box.

Supposing you are using [Monolog](https://github.com/Seldaek/monolog):

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

All the calls made currently default to the sandbox (aka `demo`) environment. To go live, simply add these lines:

```php
$soldo = new \Soldo\Soldo([
    'client_id' => 'Eu97aMWTV3ta9AchozCozGn15XiX6t5x',
    'client_secret' => 'msNE5I1BnSkWBHPVRJDMYqKvTKRfCS4a',
    'environment' => 'live'
], $logger);
```

## Next step
- [Retrieve collections](./collections.md)
