# Retrieve collections

These examples demonstrate how you can easily obtaining the collections provided by the API. Each collections will be an array containing the list of the requested resources.

It assumes that you have already instantiated a `Soldo` object.

## Obtaining the Employees list

To obtain the employees list simply do as follow

```php
try {
    $collection = $soldo->getEmployees();
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}

// each item of $collection is of type \Soldo\Resource\Employee 
var_dump($collection);
```

Accessing to other collections is pretty similar. Below you find an example for each collection provided by the API.


## Obtaining the Expense Centres list

```php
try {
    $collection = $soldo->getExpenseCentres();
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}

// each item of $collection is of type \Soldo\Resource\ExpenseCentre
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

Each of the methods seen above support cursors based pagination. You can request a specific page and the items per page (note that the max allowed is 50)

In case you want, for example, accessing the second page of the Card collection, simply do as follow

```php
try {
    $collection = $soldo->getCards(1); // pagination is 0 based
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}
```

Supposing you want 10 items per page

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
