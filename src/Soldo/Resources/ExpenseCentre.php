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
class ExpenseCentre extends Resource
{

    /**
     * @var string
     */
    protected $basePath = '/expensecentres/{id}';

    /**
     * @var array
     */
    protected $whiteListed = [
        'custom_reference_id',
        'assignee',
    ];
}
