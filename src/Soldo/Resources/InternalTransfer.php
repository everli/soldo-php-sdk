<?php

namespace Soldo\Resources;

use Soldo\Exceptions\SoldoInternalTransferException;

/**
 * Class ExpenseCentre
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
     * @var string
     */
    protected $basePath = '/wallets/internalTransfer/{fromWalletId}/{toWalletId}';

    /**
     * @var array
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
        return hash(
            'sha512',
            $this->amount . $this->currency . $this->fromWalletId . $this->toWalletId . $internalToken
        );
    }
}
