<?php

namespace Soldo\Resources;

/**
 * Class Group
 * @package Soldo\Resources
 *
 * @property string id
 * @property string name
 * @property string custom_reference_id
 * @property string type
 * @property string note
 * @property string creation_time
 * @property array members
 * @property array wallets
 * @property array cards
 */
class Group extends Resource
{
    /**
     * @inheritDoc
     */
    protected static $basePath = '/groups';

    /**
     * @inheritDoc
     */
    protected $path = '/{id}';
}
