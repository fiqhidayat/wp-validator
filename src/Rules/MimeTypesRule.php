<?php

namespace Fiqhidayat\WPValidator\Rules;

class MimeTypesRule extends AbstractRule
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
        if (empty($parameters)) {
            return false;
        }

        if (!isset($_FILES[$attribute])) {
            return false;
        }

        $file = $_FILES[$attribute];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        // Get the file's MIME type
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $file['tmp_name']);
        finfo_close($fileInfo);

        // Check if the MIME type matches any of the allowed MIME types
        return in_array($mimeType, $parameters);
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
        $mimeTypes = implode(', ', $parameters);

        return "The {$attribute} must be a file with MIME type: {$mimeTypes}.";
    }
}
