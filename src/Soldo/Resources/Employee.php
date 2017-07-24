<?php

namespace Soldo\Resources;

/**
 * Class Employee
 * @package Soldo\Resources
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
