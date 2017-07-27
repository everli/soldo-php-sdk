<?php

namespace Soldo\Resources;

/**
 * Class Employees
 * @package Soldo\Resources
 */
final class Employees extends SoldoCollection
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
