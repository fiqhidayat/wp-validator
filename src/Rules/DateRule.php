<?php

namespace Fiqhidayat\WPValidator\Rules;

class DateRule extends AbstractRule
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
        if (!is_string($value) && !is_numeric($value) && !($value instanceof \DateTimeInterface)) {
            return false;
        }

        if ($value instanceof \DateTimeInterface) {
            return true;
        }

        if (strtotime($value) === false) {
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
        return 'The :attribute is not a valid date.';
    }
}
