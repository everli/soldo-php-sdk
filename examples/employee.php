<?php


require 'bootstrap.php';



$employees = $soldo->getEmployees();
foreach ($employees as $em) {
    /** @var \Soldo\Resources\Employee $em */
    var_dump($em->toArray());
}

echo "\n";

// get id of the first element
$id = $employees[0]->id;
var_dump($id);

// get expense centre
/** @var \Soldo\Resources\ExpenseCentre $expense_centre */
$employee = $soldo->getEmployee($id);
var_dump($employee->toArray());

// update expense centre
$data = [
    'department' => 'Random department',
    'id' => 'THIS_SHOULD_NOT_BE_UDPATED',
];

/** @var \Soldo\Resources\ExpenseCentre $updated_expense_centre */
$updated_employee = $soldo->updateEmployee($id, $data);
print_r($updated_employee->toArray());



