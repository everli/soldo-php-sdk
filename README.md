# Soldo SDK for PHP

An unofficial SDK to work with the [Soldo API](https://api-demo.soldocloud.net/documentation)

## Prerequisites
- PHP 5.5 or above
- [curl](https://secure.php.net/manual/en/book.curl.php) extension enabled

## Usage

The following examples demonstrate how you would accomplish tasks with the Soldo SDK for PHP.
First of all instantiate a new `Soldo` with your credentials:


```php
require_once __DIR__ . '/vendor/autoload.php';

$soldo = new \Soldo\Soldo([
    'client_id' => 'Eu97aMWTV3ta9AchozCozGn15XiX6t5x',
    'client_secret' => 'msNE5I1BnSkWBHPVRJDMYqKvTKRfCS4a',
]);
```

### Examples
- **Configuration**
    - [Adding a Logger](#)
    - [Going live!](#)

- **Retrieve collections**
    - [Obtaining the Employees list](#)
    - [Obtaining the Expense Centres list](#)
    - [Obtaining the Wallets list](#)
    - [Obtaining the Cards list](#)
    - [Obtaining the Transactions list](#)
    - [Handle pagination](#)
    - [Searching through a collection](#)
    
- **Retrieve resource**
    - [Obtaining the Company resource](#)
    - [Obtaining an Employee resource](#)
    - [Obtaining an Expense Centre resource](#)
    - [Obtaining a Wallet resource](#)
    - [Obtaining a Card resource](#)
    - [Obtaining a Transaction resource](#)
    
- **Money transfer**
    - [Transferring money from a Wallet to another](#)
   
- **Card rules**
    - [Obtaining the card Rules](#)
    - [Updating card rules](#)
      
    
## Tests

1. [Composer](https://getcomposer.org/) is a prerequisite for running the tests. Install composer globally, then run `composer install` to install required files.
2. Create `tests/SoldoTestCredentials.php` from `tests/SoldoTestCredentials.php.dist` and edit it to add your demo environment credentials.
3. The tests can be executed by running this command from the root directory:

```bash
$ ./vendor/bin/phpunit
```


