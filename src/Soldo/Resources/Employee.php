<?php

namespace Soldo\Resources;

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
class Employee extends Resource
{

    /**
     * @inheritDoc
     */
    protected static $basePath = '/employees';

    /**
     * @inheritDoc
     */
    protected $path = '/{id}';

    /**
     * @inheritDoc
     */
    protected $whiteListed = [
        'custom_reference_id',
        'department',
    ];
}
