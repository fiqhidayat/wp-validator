<?php

namespace Fiqhidayat\WPValidator\Rules;

class DifferentRule extends AbstractRule
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
        if (!isset($parameters[0])) {
            return false;
        }

        $otherField = $parameters[0];
        $otherValue = isset($validator->attributes()[$otherField]) ? $validator->attributes()[$otherField] : null;

        return $value !== $otherValue;
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
        $otherField = isset($parameters[0]) ? $parameters[0] : '';

        return "The {$attribute} and {$otherField} must be different.";
    }
}
