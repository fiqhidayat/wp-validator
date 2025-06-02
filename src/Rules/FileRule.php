<?php

namespace Fiqhidayat\WPValidator\Rules;

class FileRule extends AbstractRule
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
        // For WordPress, we'll check if the value is an upload from $_FILES
        if (!isset($_FILES[$attribute])) {
            return false;
        }

        $file = $_FILES[$attribute];

        // Check if upload was successful
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Check if it's a valid file
        return is_uploaded_file($file['tmp_name']);
    }

    /**
     * Get the default error message for the rule.
     *
     * @return string
     */
    protected function getMessage()
    {
        return 'The :attribute must be a valid file upload.';
    }
}
