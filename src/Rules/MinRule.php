<?php

namespace Fiqhidayat\WPValidator\Rules;

class MinRule extends AbstractRule
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
        if (!isset($parameters[0]) || !is_numeric($parameters[0])) {
            return false;
        }

        $min = (int) $parameters[0];

        if (is_numeric($value)) {
            return $value >= $min;
        }

        if (is_string($value)) {
            return mb_strlen($value) >= $min;
        }

        if (is_array($value)) {
            return count($value) >= $min;
        }

        return false;
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
        $min = isset($parameters[0]) ? $parameters[0] : 0;

        if (is_numeric($value)) {
            return "The {$attribute} must be at least {$min}.";
        }

        if (is_string($value)) {
            return "The {$attribute} must be at least {$min} characters.";
        }

        if (is_array($value)) {
            return "The {$attribute} must have at least {$min} items.";
        }

        return "The {$attribute} is invalid.";
    }
}
