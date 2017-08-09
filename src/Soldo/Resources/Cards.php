<?php

namespace Soldo\Resources;

/**
 * Class Cards
 * @package Soldo\Resources
 */
class Cards extends SoldoCollection
{
    /**
     * @var string
     */
    protected $path = '/cards';

    /**
     * @var string
     */
    protected $itemType = Card::class;
}
