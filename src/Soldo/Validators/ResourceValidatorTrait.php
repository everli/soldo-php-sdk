<?php

namespace Soldo\Validators;

use Soldo\Exceptions\SoldoInvalidResourceException;
use Soldo\Resources\Resource;

/**
 * Trait ResourceValidatorTrait
 * @package Soldo\Validators
 */
trait ResourceValidatorTrait
{
    /**
     * Check that given class exists and extends Resource class
     *
     * @param $className
     * @throws SoldoInvalidResourceException
     */
    protected function validateClassName($className)
    {
        if (!class_exists($className) ||
            !is_subclass_of($className, Resource::class)) {
            throw new SoldoInvalidResourceException(
                $className . ' is not a valid Soldo resource class'
            );
        }
    }
}
