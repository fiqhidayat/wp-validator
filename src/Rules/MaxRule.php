<?php

namespace Fiqhidayat\WPValidator\Rules;

class MaxRule extends AbstractRule
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

        $max = (int) $parameters[0];

        if (is_numeric($value)) {
            return $value <= $max;
        }

        if (is_string($value)) {
            return mb_strlen($value) <= $max;
        }

        if (is_array($value)) {
            return count($value) <= $max;
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
        $max = isset($parameters[0]) ? $parameters[0] : 0;

        if (is_numeric($value)) {
            return "The {$attribute} may not be greater than {$max}.";
        }

        if (is_string($value)) {
            return "The {$attribute} may not be greater than {$max} characters.";
        }

        if (is_array($value)) {
            return "The {$attribute} may not have more than {$max} items.";
        }

        return "The {$attribute} is invalid.";
    }
}
