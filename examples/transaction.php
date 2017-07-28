<?php


require 'bootstrap.php';

$transactions = $soldo->getTransactions();

$id = $transactions[0]->id;
$transaction = $soldo->getTransaction($id, ['showDetails' => 'true']);
dump($transaction);
