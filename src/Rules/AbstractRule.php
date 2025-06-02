<?php

namespace Fiqhidayat\WPValidator\Rules;

use Fiqhidayat\WPValidator\Rule;

abstract class AbstractRule implements Rule
{
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
        //replace :attribute with the actual attribute name
        if (strpos($this->getMessage(), ':attribute') === false) {
            return $this->getMessage();
        }

        return str_replace(':attribute', $attribute, $this->getMessage());
    }

    /**
     * Get the default error message for the rule.
     *
     * @return string
     */
    protected function getMessage()
    {
        return 'The :attribute field is invalid.';
    }
}
