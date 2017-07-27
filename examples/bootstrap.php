<?php

require dirname(__DIR__) . '/vendor/autoload.php';

date_default_timezone_set('Europe/Rome');
error_reporting(E_ALL);
ini_set('display_errors', '1');

// instantiate Soldo
$soldo = new \Soldo\Soldo([
    'client_id' => '9a1afd90e10043adbb8a0ac188d150e5',
    'client_secret' => '21d350a8014640eb989b3f2e8c39139f',
]);
