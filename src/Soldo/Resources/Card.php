<?php

namespace Soldo\Resources;

/**
 * Class Card
 * @package Soldo\Resources
 *
 * @property string id
 * @property string name
 * @property string masked_pan
 * @property string card_holder
 * @property string expiration_date
 * @property string type
 * @property string status
 * @property string owner_type
 * @property string owner_public_id
 * @property string wallet_id
 * @property string currency_code
 * @property string emboss_line4
 * @property string custom_reference_id
 * @property bool active
 */
class Card extends SoldoResource
{

    /**
     * @var string
     */
    protected $basePath = '/cards';

    /**
     * @var array
     */
    protected $relationships = [
        'rules' => Rule::class,
    ];
}
