<?php

namespace Soldo\Resources;

/**
 * Class Rule
 * @package Soldo\Resources
 *
 * @property string name
 * @property bool enabled
 * @property float amount
 */
class Rule extends Resource
{
    /**
     * @var string
     */
    protected static $basePath = '/rules';
}
