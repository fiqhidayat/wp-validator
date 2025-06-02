<?php

namespace Fiqhidayat\WPValidator\Rules;

class FilledRule extends AbstractRule
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
        $attributes = $validator->attributes();

        // If the field is not present in the data, it passes
        if (!array_key_exists($attribute, $attributes)) {
            return true;
        }

        // Otherwise, it should not be empty
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
        return 'The :attribute field must not be empty when it is present.';
    }
}
