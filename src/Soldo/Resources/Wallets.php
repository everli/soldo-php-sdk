<?php

namespace Soldo\Resources;

/**
 * Class Wallets
 * @package Soldo\Resources
 */
final class Wallets extends SoldoCollection
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
