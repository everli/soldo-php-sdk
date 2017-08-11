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
    protected $basePath = '/company';

    /**
     * Override method to avoid appending id since it is not expected by Soldo API
     *
     * @return string
     */
    public function getRemotePath()
    {
        $this->validateBasePath();

        return $this->basePath;
    }
}
