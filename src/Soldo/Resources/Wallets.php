<?php

namespace Soldo\Resources;

/**
 * Class Wallets
 * @package Soldo\Resources
 */
class Wallets extends SoldoCollection
{
    /**
     * @var string
     */
    protected $path = '/wallets';

    /**
     * @var string
     */
    protected $itemType = Wallet::class;
}
