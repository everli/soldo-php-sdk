<?php

namespace Soldo\Resources;

/**
 * Class Employees
 * @package Soldo\Resources
 */
class Employees extends Collection
{
    /**
     * @var string
     */
    protected $path = '/employees';

    /**
     * @var string
     */
    protected $itemType = Employee::class;
}
