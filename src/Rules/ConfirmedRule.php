<?php

namespace Fiqhidayat\WPValidator\Rules;

class ConfirmedRule extends AbstractRule
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
        $confirmedField = $attribute . '_confirmation';
        $confirmedValue = isset($validator->attributes()[$confirmedField]) ? $validator->attributes()[$confirmedField] : null;

        return $value === $confirmedValue;
    }

    /**
     * Get the default error message for the rule.
     *
     * @return string
     */
    protected function getMessage()
    {
        return 'The :attribute confirmation does not match.';
    }
}
