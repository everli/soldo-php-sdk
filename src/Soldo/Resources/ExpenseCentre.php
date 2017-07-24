<?php

namespace Soldo\Resources;

/**
 * Class ExpenseCentre
 * @package Soldo\Resources
 */
final class ExpenseCentre extends SoldoResource
{

    /**
     * Define resource URL
     */
    const RESOURCE_PATH = '/expensecentres';

    /**
     * Define editable property according to API
     */
    const EDITABLE = [
        'custom_reference_id',
        'assignee',
    ];


}
