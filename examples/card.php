<?php


require 'bootstrap.php';



$cards = $soldo->getCards();
var_dump($cards[0]->id);

$id = $cards[0]->id;
$rules = $soldo->getCardRules($id);
print_r($rules);



