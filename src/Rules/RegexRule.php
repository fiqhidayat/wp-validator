<?php

namespace Fiqhidayat\WPValidator\Rules;

class RegexRule extends AbstractRule
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
        if (!is_string($value) && !is_numeric($value)) {
            return false;
        }

        if (!isset($parameters[0])) {
            return false;
        }

        $pattern = $parameters[0];

        // If the pattern does not include delimiters, add them
        if ($pattern[0] !== $pattern[strlen($pattern) - 1] || preg_match('/^[a-zA-Z0-9]/', $pattern[0])) {
            $pattern = '/' . $pattern . '/';
        }

        return (bool) preg_match($pattern, $value);
    }

    /**
     * Get the default error message for the rule.
     *
     * @return string
     */
    protected function getMessage()
    {
        return 'The :attribute format is invalid.';
    }
}
