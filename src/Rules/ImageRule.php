<?php

namespace Fiqhidayat\WPValidator\Rules;

class ImageRule extends FileRule
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
        // First make sure it's a valid file upload
        if (!parent::passes($attribute, $value, $parameters, $validator)) {
            return false;
        }

        // Check if the file is an image
        $file = $_FILES[$attribute];
        $fileInfo = getimagesize($file['tmp_name']);

        if ($fileInfo === false) {
            return false;
        }

        // Valid image types
        $validMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/bmp',
            'image/webp',
            'image/svg+xml'
        ];

        return in_array($fileInfo['mime'], $validMimeTypes);
    }

    /**
     * Get the default error message for the rule.
     *
     * @return string
     */
    protected function getMessage()
    {
        return 'The :attribute must be a valid image file.';
    }
}
