<?php

namespace Fiqhidayat\WPValidator\Rules;

class NullableRule extends AbstractRule
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
        // A nullable rule always passes; it just marks the field as optional
        // The real validation is handled in the validateRule method in Validator
        return true;
    }

    /**
     * Get the default error message for the rule.
     *
     * @return string
     */
    protected function getMessage()
    {
        return 'The :attribute field is nullable.';
    }
}
