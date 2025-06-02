<?php

namespace Fiqhidayat\WPValidator\Rules;

class RequiredRule extends AbstractRule
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
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif (is_array($value) && count($value) < 1) {
            return false;
        }

        return true;
    }

    /**
     * Get the default error message for the rule.
     *
     * @return string
     */
    protected function getMessage()
    {
        return 'The :attribute field is required.';
    }
}
