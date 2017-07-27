<?php

namespace Soldo\Resources;

/**
 * Class Cards
 * @package Soldo\Resources
 */
final class Cards extends SoldoCollection
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
