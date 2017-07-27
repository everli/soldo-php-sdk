<?php

namespace Soldo\Resources;

/**
 * Class ExpenseCentres
 * @package Soldo\Resources
 */
final class ExpenseCentres extends SoldoCollection
{
    /**
     * @var string
     */
    protected $path = '/expensecentres';

    /**
     * @var string
     */
    protected $itemType = ExpenseCentre::class;
}
