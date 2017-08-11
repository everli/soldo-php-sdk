<?php

namespace Soldo\Resources;

/**
 * Class Transactions
 * @package Soldo\Resources
 */
class Transactions extends Collection
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
