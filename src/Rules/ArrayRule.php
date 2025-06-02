<?php

namespace Fiqhidayat\WPValidator\Rules;

use Fiqhidayat\WPValidator\Rule;

class ArrayRule extends AbstractRule
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
        return is_array($value);
    }

    /**
     * Get the default error message for the rule.
     *
     * @return string
     */
    protected function getMessage()
    {
        return 'The :attribute must be an array.';
    }
}
