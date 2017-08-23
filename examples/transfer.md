# Money transfer

With `Soldo` you can easily transfer money from a wallet into another.
For doing this you need to know:
- Both the **wallet sender id** and the **wallet receiver id**
- The **amount** to transfer and the **currency code**
- An **internal token** provided by Soldo

Once you have all of this stuff you can transfer money as follows:

```php
try {
    $transfer = $soldo->transferMoney(
                'f086f25b-1526-11e7-9287-0a89c8769141',
                'f086f47f-1526-11e7-9287-0a89c8769141',
                50,
                'EUR',
                'N7OME5XJDWcp9eW7OlaYGrkc3PcCf1Ng'
            );
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}
```

If everything worked fine, `$transfer` will be an instance of `\Soldo\Resources\InternalTransfer` that will have the following properties:

`float amount`
The amount of transferred money

`string currency`
The currency of the transferred amount

`string datetime`
The date time of the operation

`Wallet from_wallet`
The from wallet object after the transfer

`Wallet to_wallet`
The from wallet object before the transfer

Printing out `$transfer->toArray()` will output:

```
Array
(
    [fromWalletId] => f086f47f-1526-11e7-9287-0a89c8769141
    [toWalletId] => f086f25b-1526-11e7-9287-0a89c8769141
    [amount] => 5
    [currency] => EUR
    [datetime] => 2017-08-18T12:28:14.381Z
    [from_wallet] => Array
        (
            [id] => f086f47f-1526-11e7-9287-0a89c8769141
            [name] => EURO
            [currency_code] => EUR
            [available_amount] => 4623
            [blocked_amount] => 0
            [primary_user_type] => expensecentre
            [primary_user_public_id] => SDMD7784-000002
            [visible] => 1
        )

    [to_wallet] => Array
        (
            [id] => f086f25b-1526-11e7-9287-0a89c8769141
            [name] => EURO
            [currency_code] => EUR
            [available_amount] => 15299
            [blocked_amount] => 0
            [primary_user_type] => company
            [visible] => 1
        )

)
```

## Next step
- [Card rules](./card-rules.md)
