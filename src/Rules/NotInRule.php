<?php

namespace Fiqhidayat\WPValidator\Rules;

class NotInRule extends AbstractRule
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
        return !in_array($value, $parameters, true);
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
        $values = implode(', ', $parameters);

        return "The {$attribute} is invalid. It cannot be one of: {$values}.";
    }
}
