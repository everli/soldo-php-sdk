# Webhooks

Use webhooks to be notified about events that happen in a Soldo Business account.

Some events (e.g. a payment or any other type of transaction like an ATM withdrawal) are not the result of a direct API request so those events can be managed with webhooks.  
Each time an event occurs a POST call is made to the registered webhook.

In order to register a webhook you need to contact Soldo communicating them the endpoint URL (note that your server must provide a valid SSL server certificate).

Once you did that you'll start receiving notifications to your webhook.   
Of course you will need to check and validate the authenticity of the Soldo fingerprint using the `token` released by Soldo.
To do this you need to access to the header parameters Soldo send to your endpoint:

`X-Soldo-Fingerprint-Order`   
The comma separeted fields signed with SHA512, something like `id,wallet_id,status,transaction_sign,token` (it always contains `token` as one of the parameters)

`X-Soldo-Fingerprint`   
The fingerprint generated with SHA512

The endpoint you expose will receive a JSON Payload similar to:

```
{
   "event_type":"Transaction",
   "event_name":"card_authorization",
   "data":{
      "id":"1309-189704842-1503070696138",
      "wallet_id":"f086f47f-1526-11e7-9287-0a89c8769141",
      "status":"Settled",
      "category":"Refund",
      "transaction_sign":"Positive",
      "amount":56,
      "amount_currency":"EUR",
      "tx_amount":50,
      "tx_amount_currency":"GBP",
      "fee_amount":0,
      "fee_currency":"EUR",
      "auth_exchange_rate":1,
      "date":"2017-08-18T14:36:00",
      "settlement_date":"2017-08-18T14:36:31Z",
      "merchant":{
         "name":"PRET A MANGER LONDON GBR",
         "raw_name":"PRET A MANGER LONDON GBR"
      },
      "merchant_category":{
         "mcc":"5812"
      },
      "tags":[

      ],
      "card_id":"f275d49c-1526-11e7-9287-0a89c8769141",
      "masked_pan":"999999******3706",
      "owner_id":"SDMD7784-000002",
      "custom_reference_id":"sdfgsfgsdfg",
      "owner_type":"expensecentre"
   }
}
```
Your endpoint should return a `2xx` HTTP status code.

These examples demonstrate how you can easily receiving a notification using the SDK.  

## Receiving a webhook notification

In the code we are using some vanilla PHP code for accessing header information and raw json data but if you're using a framework you can (and probably should) use the built-in request methods.

```php
try {
    // get raw json data and cast to array
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    
    // get necessary headers
    $headers = getallheaders();
    $fingerprintOrder = $headers['X-Soldo-Fingerprint-Order'];
    $fingerprint = $headers['X-Soldo-Fingerprint'];
    

    $event = new \Soldo\SoldoEvent(
    	$data, 
    	$fingerprint,
    	$fingerprintOrder,
    	'N7OME5XJDWcp9eW7OlaYGrkc3PcCf1Ng'
    	);
} catch (\Soldo\Exceptions\SoldoException $e) {
    echo 'An error has occured trying to catch an event: ' . $e->getMessage();
}


switch ($event->type()) {
	// refund transaction events
	case 'transaction.refund_settled':
		$resource = $event->get();
		break;
	
	// payment transaction events
	case 'transaction.payment_authorized':
	case 'transaction.payment_declined':
	case 'transaction.payment_settled':
	    $resource = $event->get();
    	break;
		
	// withdrawal transaction events
	case 'transaction.withdrawal_authorized':
	case 'transaction.withdrawal_declined':
	case 'transaction.withdrawal_settled':
	    $resource = $event->get();
		   break;
		
	default:
	    $resource = null;
	    break;
}

// $resource will be a \Soldo\Resources\Transaction object
var_dump($resource);

```

The code above outputs:

```
Array
(
    [id] => 1309-189704842-1503070696138
    [wallet_id] => f086f47f-1526-11e7-9287-0a89c8769141
    [status] => Settled
    [category] => Refund
    [transaction_sign] => Positive
    [amount] => 56
    [amount_currency] => EUR
    [tx_amount] => 50
    [tx_amount_currency] => GBP
    [fee_amount] => 0
    [fee_currency] => EUR
    [auth_exchange_rate] => 1
    [date] => 2017-08-18T14:36:00
    [settlement_date] => 2017-08-18T14:36:31Z
    [merchant] => Array
        (
            [name] => PRET A MANGER LONDON GBR
            [raw_name] => PRET A MANGER LONDON GBR
        )

    [merchant_category] => Array
        (
            [mcc] => 5812
        )

    [tags] => Array
        (
        )

    [card_id] => f275d49c-1526-11e7-9287-0a89c8769141
    [masked_pan] => 999999******3706
    [owner_id] => SDMD7784-000002
    [custom_reference_id] => sdfgsfgsdfg
    [owner_type] => expensecentre
)

```

## Index
- [Back to README](../README.md)
