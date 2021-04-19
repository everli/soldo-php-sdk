# Retrieve collections

These examples demonstrate how you can easily obtain the collections provided by the API. Each collection will be an array containing the list of the requested resources.

It assumes that you have already instantiated a `Soldo` object.

## Obtaining the Employees list

To obtain the employees list, use code like the following:

```php
try {
    $collection = $soldo->getEmployees();
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}

// each item of $collection is of type \Soldo\Resource\Employee
var_dump($collection);
```

Accessing the other collections is pretty similar. Below you can find an example for each collection provided by the API.


## Obtaining the Groups list

```php
try {
    $collection = $soldo->getGroups();
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}

// each item of $collection is of type \Soldo\Resource\Group
var_dump($collection);
```

## Obtaining the Wallets list

```php
try {
    $collection = $soldo->getWallets();
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}

// each item of $collection is of type \Soldo\Resource\Wallet
var_dump($collection);
```

## Obtaining the Cards list

```php
try {
    $collection = $soldo->getCards();
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}

// each item of $collection is of type \Soldo\Resource\Card
var_dump($collection);
```


## Obtaining the Transactions list

```php
try {
    $collection = $soldo->getTransactions();
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}

// each item of $collection is of type \Soldo\Resource\Transaction
var_dump($collection);
```

## Handle pagination

Each of the methods seen above support cursor based pagination. You can request a specific page and the number of items per page. Note that the maximum number of items allowed per page is 50.

In case you want, for example, accessing the second page of the Card collection, simply use this code:

```php
try {
    $collection = $soldo->getCards(1); // pagination is 0 based
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}
```

Supposing you want 10 items per page:

```php
try {
    $collection = $soldo->getCards(1, 10);
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}
```


## Searching through a collection

It is possible to filter a collection passing the array of parameters you want to search for (see [Soldo documentation](https://api-demo.soldocloud.net/documentation) for the full list of supported params for each collection).


```php
try {
    $collection = $soldo->getWallets(0, 50, ['type' => 'employee']);
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}
```

## Next step
- [Retrieve resource](./resources.md)
