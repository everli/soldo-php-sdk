<?php

namespace Soldo\Resources;

/**
 * Class ExpenseCentre
 * @package Soldo\Resources
 *
 * @property string id
 * @property string name
 * @property string assignee
 * @property string custom_reference_id
 * @property string status
 * @property boolean visible
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
