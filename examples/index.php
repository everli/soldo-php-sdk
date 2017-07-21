<?php
/**
 * Created by PhpStorm.
 * User: ilpes
 * Date: 20/07/17
 * Time: 10:38
 */

require dirname(__DIR__) . '/vendor/autoload.php';

// Suppress DateTime warnings, if not set already
date_default_timezone_set(@date_default_timezone_get());
// Adding Error Reporting for understanding errors properly
error_reporting(E_ALL);
ini_set('display_errors', '1');


$s = new \Soldo\Soldo([
    'client_id' => '9a1afd90e10043adbb8a0ac188d150e5',
    'client_secret' => '21d350a8014640eb989b3f2e8c39139f'
]);

$expense_centres = $s->getExpenseCentres();
foreach ($expense_centres as $ec) {
    echo $ec->id . "\n";
    echo $ec->name . "\n";
}



