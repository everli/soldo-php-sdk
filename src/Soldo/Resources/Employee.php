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
final class Employee extends SoldoResource
{

    /**
     * Define resource URL
     */
    const RESOURCE_PATH = '/employees';

    /**
     * Define editable property according to API
     */
    const EDITABLE = [
        'custom_reference_id',
        'department',
    ];


}
