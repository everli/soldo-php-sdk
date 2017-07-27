<?php


require 'bootstrap.php';

$c = $soldo->getCompany();
/** @var \Soldo\Resources\Company $em */
var_dump($c->toArray());
