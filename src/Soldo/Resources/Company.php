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
final class Company extends SoldoResource
{

    /**
     * @var string
     */
    protected $basePath = '/company';

    /**
     * Override method to avoid appending id since it is not expected by Soldo API
     *
     * @return string
     */
    public function getRemotePath()
    {
        return $this->basePath;
    }
}
