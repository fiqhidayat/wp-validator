<?php

namespace Fiqhidayat\WPValidator\Rules;

use Fiqhidayat\WPValidator\Rule;

class ArrayItemRule extends AbstractRule
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
        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $item) {
            if (!$this->validateItem($item, $parameters)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate each item in the array.
     *
     * @param mixed $item
     * @param array $parameters
     * @return bool
     */
    protected function validateItem($item, array $parameters)
    {
        // Implement item validation logic based on parameters
        return true; // Placeholder for actual validation logic
    }
}
