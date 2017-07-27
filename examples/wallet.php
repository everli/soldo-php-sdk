<?php


require 'bootstrap.php';

$wallets = $soldo->getWallets(0, 100, ['type' => 'employee']);
dump($wallets);

foreach ($wallets as $w) {
    /** @var \Soldo\Resources\Wallet $w */
    dump($w);
}

echo PHP_EOL;

// need to check with a foreach because the custom_reference_id can be null
$custom_reference_id = null;
foreach ($wallets as $wallet) {
    /** @var \Soldo\Resources\Wallet $wallet */
    if ($wallet->custom_reference_id !== null) {
        $custom_reference_id = $wallet->custom_reference_id;
    }
}
dump($custom_reference_id);

// get id of the first element
$id = $wallets[0]->id;
dump($id);

//// get wallet
/** @var \Soldo\Resources\Wallet $expense_centre */
$wallet = $soldo->getWallet($id);
dump($wallet);

//// get wallet
/** @var \Soldo\Resources\SoldoCollection $wallet_filtered_list */
$wallet_filtered_list = $soldo->getWallets(100, 100, ['customreferenceId' => $custom_reference_id]);
dump($wallet_filtered_list);
