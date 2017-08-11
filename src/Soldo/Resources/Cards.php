<?php

namespace Soldo\Resources;

/**
 * Class Cards
 * @package Soldo\Resources
 */
class Cards extends Collection
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
