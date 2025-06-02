<?php

namespace Fiqhidayat\WPValidator\Rules;

class DimensionsRule extends AbstractRule
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

        $dimensions = @getimagesize($file['tmp_name']);

        if ($dimensions === false) {
            return false;
        }

        $width = $dimensions[0];
        $height = $dimensions[1];

        $constraints = $this->parseParameters($parameters);

        if (isset($constraints['width']) && $constraints['width'] != $width) {
            return false;
        }

        if (isset($constraints['height']) && $constraints['height'] != $height) {
            return false;
        }

        if (isset($constraints['min_width']) && $constraints['min_width'] > $width) {
            return false;
        }

        if (isset($constraints['min_height']) && $constraints['min_height'] > $height) {
            return false;
        }

        if (isset($constraints['max_width']) && $constraints['max_width'] < $width) {
            return false;
        }

        if (isset($constraints['max_height']) && $constraints['max_height'] < $height) {
            return false;
        }

        if (isset($constraints['ratio'])) {
            list($numerator, $denominator) = array_pad(explode('/', $constraints['ratio'], 2), 2, 1);

            $expectedRatio = $numerator / $denominator;
            $actualRatio = $width / $height;

            // Allow a small difference in ratio due to rounding
            return abs($expectedRatio - $actualRatio) < 0.01;
        }

        return true;
    }

    /**
     * Parse the dimension parameters.
     *
     * @param array $parameters
     * @return array
     */
    protected function parseParameters($parameters)
    {
        $constraints = [];

        foreach ($parameters as $parameter) {
            if (strpos($parameter, '=') === false) {
                continue;
            }

            list($key, $value) = explode('=', $parameter, 2);

            if (is_numeric($value)) {
                $value = (int) $value;
            }

            $constraints[$key] = $value;
        }

        return $constraints;
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
        $constraints = $this->parseParameters($parameters);

        $message = "The {$attribute} image dimensions are invalid.";

        if (count($constraints) > 0) {
            $constraintStrings = [];

            foreach ($constraints as $key => $value) {
                $constraintStrings[] = "{$key}={$value}";
            }

            $message .= " Required dimensions: " . implode(', ', $constraintStrings);
        }

        return $message;
    }
}
