<?php

namespace Soldo\Resources;

use Soldo\SoldoUtilities;

/**
 * Class Employee
 * @package Soldo\Resources
 *
 * @property string id
 * @property string name
 * @property string surname
 * @property string job_title
 * @property string department
 * @property string email
 * @property string mobile
 * @property string custom_reference_id
 * @property string status
 */
final class Employee extends SoldoResource
{

    /**
     * @var string
     */
    protected $basePath = '/employees';

    /**
     * @var array
     */
    protected $whiteListed = [
        'custom_reference_id',
        'department'
    ];


}