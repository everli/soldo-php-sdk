# Retrieve resource

These examples demonstrate how you can easily obtaining a single resource once you have its own `id`. The array representation of the resource can be obtained calling `$resource->toArray()` method.

It assumes that you have already instantiated a `Soldo` object.

## Obtaining the Company resource

To get the `Company` object you don't need an `id` since it is unique.

```php
try {
    $resource = $soldo->getCompany();
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}

echo get_class($resource);
echo PHP_EOL;
print_r($resource->toArray());
```

The output of the code above is

```
Soldo\Resources\Company
Array
(
    [id] => 
    [name] => S24 Demo
    [vat_number] => 321456
    [company_account_id] => SDMD7784
)
```

## Obtaining an Employee resource

Once you have the `id` you can do as follow

```php
try {
    $resource = $soldo->getEmployee('SDMD7784-000001');
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}
```


## Obtaining an Expense Centre resource

```php
try {
    $resource = $soldo->getExpenseCentre('SDMD7784-000002');
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}
```


## Obtaining a Wallet resource

```php
try {
    $resource = $soldo->getWallet('SDMD7784-000003');
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}
```


## Obtaining a Card resource

```php
try {
    $resource = $soldo->getCard('SDMD7784-000004');
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}
```

## Obtaining a Transaction resource

```php
try {
    $resource = $soldo->getTransaction('SDMD7784-000005');
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}
```

## Next step
- [Money transfer](./transfer.md)
