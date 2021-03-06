# Soldo SDK for PHP
[![License](https://img.shields.io/badge/License-Apache%202.0-blue.svg)](https://opensource.org/licenses/Apache-2.0)

An unofficial SDK to work with the [Soldo API](https://api-demo.soldocloud.net/documentation)

## Contacts
In case you're interested in using the Soldo API, please [contact them](mailto:businessdevelopment@soldo.com) so they can support you in integrating them in your existing system.

## Prerequisites
- PHP 5.5 or above
- [curl](https://secure.php.net/manual/en/book.curl.php) extension enabled

## Usage

First of all instantiate a new `Soldo` object with your credentials:


```php
require_once __DIR__ . '/vendor/autoload.php';

$soldo = new \Soldo\Soldo([
    'client_id' => 'Eu97aMWTV3ta9AchozCozGn15XiX6t5x',
    'client_secret' => 'msNE5I1BnSkWBHPVRJDMYqKvTKRfCS4a',
]);
```

### Examples
- **Configuration**
    - [Adding a Logger](./examples/configuration.md#adding-a-logger)
    - [Going live!](./examples/configuration.md#going-live)

- **Retrieve collections**
    - [Obtaining the Employees list](./examples/collections.md#obtaining-the-employees-list)
    - [Obtaining the Expense Centres list](./examples/collections.md#obtaining-the-expense-centres-list)
    - [Obtaining the Wallets list](./examples/collections.md#obtaining-the-wallets-list)
    - [Obtaining the Cards list](./examples/collections.md#obtaining-the-cards-list)
    - [Obtaining the Transactions list](./examples/collections.md#obtaining-the-transactions-list)
    - [Handle pagination](./examples/collections.md#handle-pagination)
    - [Searching through a collection](./examples/collections.md#searching-through-a-collection)
    
- **Retrieve resource**
    - [Obtaining the Company resource](./examples/resources.md#obtaining-the-company-resource)
    - [Obtaining an Employee resource](./examples/resources.md#obtaining-an-employee-resource)
    - [Updating an Employee resource](./examples/resources.md#updating-an-employee-resource)
    - [Obtaining an Expense Centre resource](./examples/resources.md#obtaining-an-expense-centre-resource)
    - [Updating an Expense Centre resource](./examples/resources.md#updating-an--expense-centre-resource)
    - [Obtaining a Wallet resource](./examples/resources.md#obtaining-a-wallet-resource)
    - [Obtaining a Card resource](./examples/resources.md#obtaining-a-card-resource)
    - [Obtaining a Transaction resource](./examples/resources.md#obtaining-a-transaction-resource)
    
- **Money transfer**
    - [Transferring money from a Wallet to another](./examples/transfer.md)
   
- **Card rules**
    - [Obtaining the card Rules](./examples/card-rules.md#obtaining-the-card-rules)
    
- **Webhooks**
    - [Receiving a webhook notification](./examples/webhooks.md#receiving-a-webhook-notification)
      
    
## Tests

1. [Composer](https://getcomposer.org/) is a prerequisite for running the tests. Install composer globally, then run `composer install` to install required files.
2. Create `tests/SoldoTestCredentials.php` from `tests/SoldoTestCredentials.php.dist` and edit it to add your demo environment credentials.
3. The tests can be executed by running this command from the root directory:

```bash
$ ./vendor/bin/phpunit
```

## License

```
Copyright 2019 S24 S.p.A.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
```


