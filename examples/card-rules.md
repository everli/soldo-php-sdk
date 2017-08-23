# Card rules

These examples demonstrate how you can easily get ~~and change~~ the rules of one card.

Currently each card has the following rules:
- **OpenCloseMasterLock**: card disabling set up by the business console user. If enabled the OpenClose rule will be ignored. If `enabled=true` the card is NOT locked.
- **OpenClose**: card disabling set up by the employee console user. If `enabled=true` the card is NOT locked.
- **OpenCloseAfterOneTx**: if `enabled=true` the card will be disabled after each transaction.
- **Online**: if `enabled=true` all online purchases are enabled.
- **Abroad**: if `enabled=true` all the abroad purchases are enabled (ignored for online merchants).
- **CashPoint**: if `enabled=true` using the ATM is permitted.
- **MaxPerTx**: if `enabled=true` the max amount of a single transaction is the value of the `amount` property (expressed in cents).

In the next big release the following rules will be added:
- **MagstripeWithdrawal**: enabling / disabling ATM withdrawal if ATM uses the magnetic strip
- **ATMDailyLimits**: daily limit of ATM withdrawal
- **ATMWeeklyLimits**: weekly limit of ATM withdrawal
- **ATMMonthlyLimits**:  monthly limit of ATM withdrawal
- **Contactless**: enabling / disabling contactless transactions (without PIN)
- **BlacklistMCC**: enabling / disabling the merchant blacklist

## Obtaining the card Rules

Once you have the card `id` you can obtain the card rules simply doing:

```php
try {
    $collection = $soldo->getCards();
    $rules = $soldo->getCardRules($collection[0]->id);
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'Soldo returned an error: ' . $e->getMessage();
}

foreach ($rules as $rule) {
    print_r($rule->toArray());
}
```

The code above outputs:

```
Array
(
    [name] => OpenCloseMasterLock
    [enabled] => 1
)
Array
(
    [name] => OpenClose
    [enabled] => 1
)
Array
(
    [name] => OpenCloseAfterOneTx
    [enabled] =>
)
Array
(
    [name] => Online
    [enabled] => 1
)
Array
(
    [name] => Abroad
    [enabled] => 1
)
Array
(
    [name] => CashPoint
    [enabled] =>
)
Array
(
    [name] => MaxPerTx
    [enabled] => 1
    [amount] => 50
)

```

## Next step
- [Webhooks](./webhooks.md)
