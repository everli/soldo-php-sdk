<?php


require 'bootstrap.php';



$wallets = $soldo->getWallets();
foreach ($wallets as $w) {
    /** @var \Soldo\Resources\Wallet $w */
    var_dump($w->toArray());
}

echo "\n";

// need to check with a foreach because the custom_reference_id can be null
$customreferenceId = null;
foreach ($wallets as $wallet){
    /** @var \Soldo\Resources\Wallet $wallet */
    if($wallet->custom_reference_id !== null) {
        $customreferenceId = $wallet->custom_reference_id;
    }
}
var_dump($customreferenceId);

// get id of the first element
$id = $wallets[0]->id;
var_dump($id);

//// get wallet
/** @var \Soldo\Resources\Wallet $expense_centre */
$wallet = $soldo->getWallet($id);
var_dump($wallet->toArray());

//// get wallet
/** @var \Soldo\Resources\SoldoCollection $wallet_filtered_list */
$wallet_filtered_list = $soldo->getWallets(['customreferenceId' => '124']);
var_dump($wallet_filtered_list);





