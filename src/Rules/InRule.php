<?php

namespace Fiqhidayat\WPValidator\Rules;

class InRule extends AbstractRule
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
        if (empty($parameters)) {
            return false;
        }

        return in_array($value, $parameters, true);
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

        return "The selected {$attribute} is invalid. Valid values are: {$values}.";
    }
}
