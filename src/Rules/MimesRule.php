<?php

namespace Fiqhidayat\WPValidator\Rules;

class MimesRule extends AbstractRule
{
    /**
     * Common MIME types
     * 
     * @var array
     */
    protected $mimeTypes = [
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'txt' => 'text/plain',
        'csv' => 'text/csv',
        'html' => 'text/html',
        'xml' => 'application/xml',
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mp4',
        'json' => 'application/json',
    ];

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

        // Check if the MIME type matches any of the allowed extensions
        $allowedMimeTypes = [];

        foreach ($parameters as $extension) {
            if (isset($this->mimeTypes[$extension])) {
                $allowedMimeTypes[] = $this->mimeTypes[$extension];
            }
        }

        return in_array($mimeType, $allowedMimeTypes);
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
        $extensions = implode(', ', $parameters);

        return "The {$attribute} must be a file of type: {$extensions}.";
    }
}
