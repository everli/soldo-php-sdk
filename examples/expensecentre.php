<?php


require 'bootstrap.php';



$expense_centres = $soldo->getExpenseCentres();
foreach ($expense_centres as $ec) {
    /** @var \Soldo\Resources\ExpenseCentre $ec */
    var_dump($ec->toArray());
}

echo "\n";

// get id of the first element
$id = $expense_centres[0]->id;
var_dump($id);

// get expense centre
/** @var \Soldo\Resources\ExpenseCentre $expense_centre */
$expense_centre = $soldo->getExpenseCentre($id);
var_dump($expense_centre->toArray());

// update expense centre
$data = [
    'assignee' => 'Test Assignee',
    'id' => 'THIS_SHOULD_NOT_BE_UDPATED',
];

/** @var \Soldo\Resources\ExpenseCentre $updated_expense_centre */
$updated_expense_centre = $soldo->updateExpenseCentre($id, $data);
print_r($updated_expense_centre->toArray());



