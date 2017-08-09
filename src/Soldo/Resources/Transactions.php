<?php

namespace Soldo\Resources;

/**
 * Class Transactions
 * @package Soldo\Resources
 */
class Transactions extends SoldoCollection
{
    /**
     * @var string
     */
    protected $path = '/transactions';

    /**
     * @var string
     */
    protected $itemType = Transaction::class;
}
