<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class ImageRuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Set up mock for PHP superglobals and functions
        global $_FILES;

        // Mock $_FILES for testing
        $_FILES = [
            'valid_image' => [
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'size' => 1024,
                'tmp_name' => '/tmp/phpXXXXXX',
                'error' => UPLOAD_ERR_OK
            ],
            'invalid_image' => [
                'name' => 'test.txt',
                'type' => 'text/plain',
                'size' => 1024,
                'tmp_name' => '/tmp/phpYYYYYY',
                'error' => UPLOAD_ERR_OK
            ],
            'error_file' => [
                'name' => 'error.jpg',
                'type' => 'image/jpeg',
                'size' => 1024,
                'tmp_name' => '/tmp/phpZZZZZZ',
                'error' => UPLOAD_ERR_PARTIAL
            ]
        ];

        // Mock the is_uploaded_file function
        if (!function_exists('is_uploaded_file')) {
            function is_uploaded_file($filename)
            {
                return true; // Always return true for testing
            }
        }

        // Mock the getimagesize function
        if (!function_exists('getimagesize')) {
            function getimagesize($filename)
            {
                if (strpos($filename, 'phpXXXXXX') !== false) {
                    // For valid image file
                    return [
                        0 => 800,
                        1 => 600,
                        'mime' => 'image/jpeg',
                        'bits' => 8,
                    ];
                } else if (strpos($filename, 'phpYYYYYY') !== false) {
                    // For non-image file
                    return false;
                } else {
                    return false;
                }
            }
        }
    }

    /**
     * Test that the image rule passes with valid image uploads
     */
    public function testImageRulePasses()
    {
        // Valid image upload
        $validator = new Validator(['upload' => 'valid_image_value'], ['upload' => 'image']);

        // Override the attribute key to match our $_FILES key
        $reflectionClass = new \ReflectionClass($validator);
        $attributesProperty = $reflectionClass->getProperty('attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($validator, ['valid_image' => 'valid_image_value']);

        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the image rule fails with invalid uploads
     */
    public function testImageRuleFails()
    {
        // Non-image file
        $validator = new Validator(['upload' => 'invalid_image_value'], ['upload' => 'image']);

        // Override the attribute key to match our $_FILES key
        $reflectionClass = new \ReflectionClass($validator);
        $attributesProperty = $reflectionClass->getProperty('attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($validator, ['invalid_image' => 'invalid_image_value']);

        $this->assertTrue($validator->fails());

        // File with upload error
        $validator = new Validator(['upload' => 'error_file_value'], ['upload' => 'image']);

        // Override the attribute key
        $attributesProperty->setValue($validator, ['error_file' => 'error_file_value']);

        $this->assertTrue($validator->fails());

        // File not in $_FILES
        $validator = new Validator(['missing_file' => 'some_value'], ['missing_file' => 'image']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error messages for image rule
     */
    public function testImageErrorMessage()
    {
        $validator = new Validator(['missing_file' => 'some_value'], ['missing_file' => 'image']);
        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('missing_file', $errors);
        $this->assertEquals('The missing_file must be a valid image file.', $errors['missing_file'][0]);
    }
}
