<?php

namespace Soldo\Resources;

/**
 * Class Transaction
 * @package Soldo\Resources
 *
 * @property string id
 * @property string wallet_id
 * @property string wallet_name
 * @property string status
 * @property string category
 * @property string transaction_sign
 * @property float amount
 * @property string amount_currency
 * @property float tx_amount
 * @property string tx_mount_currency
 * @property float fee_amount
 * @property string fee_currency
 * @property float exchange_rate
 * @property float auth_exchange_rate
 * @property string date
 * @property string settlement_date
 * @property object merchant
 * @property object merchant_category
 * @property array tags
 * @property string card_id
 * @property string masked_pan
 * @property string owner_id
 * @property string owner_type
 * @property string owner_name
 * @property string owner_surname
 * @property string custom_reference_id
 * @property array details
 */
class Transaction extends Resource
{

    /**
     * @inheritDoc
     */
    protected static $basePath = '/transactions';

    /**
     * @inheritDoc
     */
    protected $path = '/{id}';

    /**
     * @inheritDoc
     */
    protected $eventType = '{category}_{status}';
}
