<?php

namespace Soldo\Resources;

use Soldo\Exceptions\SoldoInternalTransferException;

/**
 * Class InternalTransfer
 * @package Soldo\Resources
 *
 * @property string fromWalletId
 * @property string toWalletId
 * @property float amount
 * @property string currency
 * @property string datetime
 * @property Wallet from_wallet
 * @property Wallet to_wallet
 */
class InternalTransfer extends Resource
{
    /**
     * @inheritDoc
     */
    protected static $basePath = '/wallets/internalTransfer';

    /**
     * @inheritDoc
     */
    protected $path = '/{fromWalletId}/{toWalletId}';

    /**
     * @inheritDoc
     */
    protected $cast = [
        'from_wallet' => Wallet::class,
        'to_wallet' => Wallet::class,
    ];

    /**
     * Generate a transfer fingerprint
     *
     * @param string $internalToken
     * @return string
     */
    public function generateFingerPrint($internalToken)
    {
        return $this->buildFingerprint([
            'amount',
            'currency',
            'fromWalletId',
            'toWalletId',
            'token',
        ], $internalToken);
    }
}
