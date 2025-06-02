<?php

namespace Fiqhidayat\WPValidator\Rules;

class SizeRule extends AbstractRule
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
        if (!isset($parameters[0]) || !is_numeric($parameters[0])) {
            return false;
        }

        $size = (int) $parameters[0];

        // For strings, check character count
        if (is_string($value)) {
            return mb_strlen($value) === $size;
        }

        // For numeric values, check the value
        if (is_numeric($value)) {
            return $value == $size;
        }

        // For arrays, check item count
        if (is_array($value)) {
            return count($value) === $size;
        }

        // For files, check file size in kilobytes
        if (isset($_FILES[$attribute])) {
            $file = $_FILES[$attribute];

            if ($file['error'] === UPLOAD_ERR_OK) {
                return round($file['size'] / 1024) === $size;
            }
        }

        return false;
    }

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
        $size = isset($parameters[0]) ? $parameters[0] : '';

        if (is_string($value)) {
            return "The {$attribute} must be {$size} characters.";
        }

        if (is_numeric($value)) {
            return "The {$attribute} must be {$size}.";
        }

        if (is_array($value)) {
            return "The {$attribute} must contain {$size} items.";
        }

        if (isset($_FILES[$attribute])) {
            return "The {$attribute} must be {$size} kilobytes.";
        }

        return "The {$attribute} is invalid.";
    }
}
