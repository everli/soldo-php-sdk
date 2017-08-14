<?php

namespace Soldo\Resources;

/**
 * Class Company
 * @package Soldo\Resources
 *
 * @property string name
 * @property string vat_number
 * @property string company_account_id
 */
class Company extends Resource
{

    /**
     * @var string
     */
    protected static $basePath = '/company';

}
