<?php

namespace Fiqhidayat\WPValidator\Rules;

class TimezoneRule extends AbstractRule
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
        if (!is_string($value)) {
            return false;
        }

        try {
            new \DateTimeZone($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get the default error message for the rule.
     *
     * @return string
     */
    protected function getMessage()
    {
        return 'The :attribute must be a valid timezone.';
    }
}
