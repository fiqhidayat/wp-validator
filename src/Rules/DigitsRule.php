<?php

namespace Fiqhidayat\WPValidator\Rules;

class DigitsRule extends AbstractRule
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

        $length = (int) $parameters[0];

        return is_numeric($value) && strlen((string) $value) === $length;
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
        $length = isset($parameters[0]) ? $parameters[0] : '';

        return "The {$attribute} must be {$length} digits.";
    }
}
