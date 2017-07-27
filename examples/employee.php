<?php


require 'bootstrap.php';

$employees = $soldo->getEmployees(-1, 1);
foreach ($employees as $em) {
    /** @var \Soldo\Resources\Employee $em */
    dump($em);
}

echo PHP_EOL;
exit;

// get id of the first element
$id = $employees[0]->id;
dump($id);

// get expense centre
/** @var \Soldo\Resources\ExpenseCentre $expense_centre */
$employee = $soldo->getEmployee($id);
dump($employee);

// update expense centre
$data = [
    'department' => 'Random department test',
    'custom_reference_id' => 'Test',
    'id' => 'THIS_SHOULD_NOT_BE_UDPATED',
];

/** @var \Soldo\Resources\ExpenseCentre $updated_expense_centre */
$updated_employee = $soldo->updateEmployee($id, $data);
dump($updated_employee);
