<?php

namespace Soldo\Resources;

/**
 * Class Wallet
 * @package Soldo\Resources
 *
 * @property string id
 * @property string name
 * @property string currency_code
 * @property float available_amount
 * @property float blocked_amount
 * @property string primary_user_type
 * @property string primary_user_public_id
 * @property string custom_reference_id
 * @property boolean visible
 */
class Wallet extends Resource
{
    /**
     * @inheritDoc
     */
    protected static $basePath = '/wallets';

    /**
     * @inheritDoc
     */
    protected $path = '/{id}';
}
