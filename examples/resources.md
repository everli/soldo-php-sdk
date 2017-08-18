# Retrieve resource

These examples demonstrate how you can easily obtaining a single resource once you have its own `id`. The array representation of the resource can be obtained calling `$resource->toArray()` method.

It assumes that you have already instantiated a `Soldo` object.

## Obtaining the Company resource

To get the `Company` object you don't need an `id` since it is unique.

```php
try {
    $company = $soldo->getCompany();
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}

echo get_class($company);
echo PHP_EOL;
print_r($company->toArray());
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


## Next step
- [Money transfer](./transfer.md)
