<?php

namespace Fiqhidayat\WPValidator\Rules;

class LessThanOrEqualRule extends AbstractRule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @param mixed $validator
     * @return bool
     */
    public function passes($attribute, $value, array $parameters, $validator)
    {
        if (count($parameters) < 1) {
            return false;
        }

        $otherField = $parameters[0];
        $otherValue = $validator->getValue($otherField);

        if (!is_numeric($value) || !is_numeric($otherValue)) {
            return false;
        }

        return $value <= $otherValue;
    }

    /**
     * Get the validation error message.
     *
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @return string
     */
    public function message($attribute, $value, array $parameters)
    {
        $otherField = isset($parameters[0]) ? str_replace('_', ' ', $parameters[0]) : '';

        return "The {$attribute} must be less than or equal to {$otherField}.";
    }
}
