<?php

namespace Fiqhidayat\WPValidator\Rules;

class DigitsBetweenRule extends AbstractRule
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
        if (count($parameters) < 2 || !is_numeric($parameters[0]) || !is_numeric($parameters[1])) {
            return false;
        }

        $min = (int) $parameters[0];
        $max = (int) $parameters[1];

        if (!is_numeric($value)) {
            return false;
        }

        $length = strlen((string) $value);

        return $length >= $min && $length <= $max;
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
        $min = isset($parameters[0]) ? $parameters[0] : '';
        $max = isset($parameters[1]) ? $parameters[1] : '';

        return "The {$attribute} must be between {$min} and {$max} digits.";
    }
}
