<?php

namespace Soldo\Resources;

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
class InternalTransfer extends SoldoResource
{
    /**
     * @var string
     */
    protected $basePath = '/wallets/internalTransfer';

    /**
     * @var array
     */
    protected $cast = [
        'from_wallet' => Wallet::class,
        'to_wallet' => Wallet::class,
    ];

    /**
     * Override method since this is an exception
     *
     * @return string
     */
    public function getRemotePath()
    {
        if ($this->fromWalletId === null) {
            throw new \BadMethodCallException(
                'Cannot retrieve remote path for ' . static::class . '.'
                . ' "fromWalletId" attribute is not defined.'
            );
        }

        if ($this->toWalletId === null) {
            throw new \BadMethodCallException(
                'Cannot retrieve remote path for ' . static::class . '.'
                . ' "toWalletId" attribute is not defined.'
            );
        }

        return $this->basePath . '/' . $this->fromWalletId . '/' . $this->toWalletId;
    }

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
