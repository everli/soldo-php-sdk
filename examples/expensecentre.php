<?php


require 'bootstrap.php';

$expense_centres = $soldo->getExpenseCentres(0, 1);
foreach ($expense_centres as $ec) {
    /** @var \Soldo\Resources\ExpenseCentre $ec */
    dump($ec);
}

echo PHP_EOL;

// get id of the first element
$id = $expense_centres[0]->id;
dump($id);

// get expense centre
/** @var \Soldo\Resources\ExpenseCentre $expense_centre */
$expense_centre = $soldo->getExpenseCentre($id);
dump($expense_centre);

// update expense centre
$data = [
    'assignee' => 'Test Assignee',
    'id' => 'THIS_SHOULD_NOT_BE_UDPATED',
];

/** @var \Soldo\Resources\ExpenseCentre $updated_expense_centre */
$updated_expense_centre = $soldo->updateExpenseCentre($id, $data);
dump($updated_expense_centre);
