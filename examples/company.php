<?php


require 'bootstrap.php';

$c = $soldo->getCompany();
/** @var \Soldo\Resources\Company $em */
dump($c->toArray());
