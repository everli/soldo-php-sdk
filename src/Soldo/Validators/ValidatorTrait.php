<?php

namespace Soldo\Validators;

/**
 * Trait ValidatorTrait
 * @package Soldo\Validators
 */
trait ValidatorTrait
{
    /**
     * To be valid rawData must be an array
     * and each array item must pass the validation against given rule.
     * Supported rules
     *      - integer
     *      - required
     *      - array
     *
     * @param $rawData
     * @param $rules
     * @return bool
     */
    protected function validateRawData($rawData, $rules)
    {
        if (!is_array($rawData)) {
            return false;
        }

        foreach ($rules as $key => $rule) {
            if (!array_key_exists($key, $rawData)) {
                return false;
            }
            $method = 'validateAgainst' . ucfirst($rule);
            if (!method_exists($this, $method)) {
                return false;
            }

            $isValid = $this->$method($rawData[$key]);
            if (!$isValid) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate against integer in a weak way
     * in fact both 1 and '1' will pass validation
     *
     * @param $value
     * @return bool
     */
    protected function validateAgainstInteger($value)
    {
        return ctype_digit((string) $value) === true;
    }

    /**
     * Validate against array
     * Empty arrays pass validation
     *
     * @param $value
     * @return bool
     */
    protected function validateAgainstArray($value)
    {
        return is_array($value);
    }

    /**
     * Validate against required
     * The value must be not null, not empty string and not false
     *
     * @param $value
     * @return bool
     */
    protected function validateAgainstRequired($value)
    {
        return $value !== null &&
            $value !== '' &&
            $value !== false;
    }
}
