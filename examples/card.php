<?php


require 'bootstrap.php';

$cards = $soldo->getCards();
dump($cards[0]->id);

$id = $cards[0]->id;
$rules = $soldo->getCardRules($id);
dump($rules);
