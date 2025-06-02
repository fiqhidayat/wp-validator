<?php

namespace Fiqhidayat\WPValidator\Rules;

class BooleanRule extends AbstractRule
{
    /**
     * The acceptable values.
     *
     * @var array
     */
    protected $acceptable = [true, false, 0, 1, '0', '1'];

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
        return in_array($value, $this->acceptable, true);
    }

    /**
     * Get the default error message for the rule.
     *
     * @return string
     */
    protected function getMessage()
    {
        return 'The :attribute field must be true or false.';
    }
}
