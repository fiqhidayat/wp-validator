<?php

namespace Fiqhidayat\WPValidator\Rules;

class AlphaRule extends AbstractRule
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
        if (!is_string($value) && !is_null($value)) {
            return false;
        }

        return preg_match('/^[\pL\pM]+$/u', $value);
    }

    /**
     * Get the default error message for the rule.
     *
     * @return string
     */
    protected function getMessage()
    {
        return 'The :attribute may only contain letters.';
    }
}
