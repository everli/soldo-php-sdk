<?php

namespace Soldo\Resources;

/**
 * Class Wallets
 * @package Soldo\Resources
 */
class Wallets extends Collection
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
