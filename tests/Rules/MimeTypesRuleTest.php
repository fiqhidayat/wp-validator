<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class MimeTypesRuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Set up mock for PHP superglobals and functions
        global $_FILES;

        // Mock $_FILES for testing
        $_FILES = [
            'jpeg_file' => [
                'name' => 'test.jpg',
                'type' => 'image/jpeg',
                'size' => 1024,
                'tmp_name' => '/tmp/phpXXXXXX',
                'error' => UPLOAD_ERR_OK
            ],
            'pdf_file' => [
                'name' => 'document.pdf',
                'type' => 'application/pdf',
                'size' => 2048,
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

        // Mock the finfo_open function
        if (!function_exists('finfo_open')) {
            function finfo_open($options)
            {
                return true;
            }
        }

        // Mock the finfo_file function
        if (!function_exists('finfo_file')) {
            function finfo_file($finfo, $filename)
            {
                if (strpos($filename, 'phpXXXXXX') !== false) {
                    return 'image/jpeg';
                } else if (strpos($filename, 'phpYYYYYY') !== false) {
                    return 'application/pdf';
                }
                return 'application/octet-stream';
            }
        }

        // Mock the finfo_close function
        if (!function_exists('finfo_close')) {
            function finfo_close($finfo)
            {
                return true;
            }
        }
    }

    /**
     * Test that the mimetypes rule passes with valid MIME types
     */
    public function testMimeTypesRulePasses()
    {
        // JPEG file matches image/jpeg MIME type
        $validator = new Validator(['upload' => 'jpeg_file_value'], ['upload' => 'mimetypes:image/jpeg,image/png']);

        // Override the attribute key to match our $_FILES key
        $reflectionClass = new \ReflectionClass($validator);
        $attributesProperty = $reflectionClass->getProperty('attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($validator, ['jpeg_file' => 'jpeg_file_value']);

        $this->assertTrue($validator->passes());

        // PDF file matches application/pdf MIME type
        $validator = new Validator(['upload' => 'pdf_file_value'], ['upload' => 'mimetypes:application/pdf,application/msword']);

        // Override the attribute key to match our $_FILES key
        $attributesProperty->setValue($validator, ['pdf_file' => 'pdf_file_value']);

        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the mimetypes rule fails with invalid MIME types
     */
    public function testMimeTypesRuleFails()
    {
        // JPEG file doesn't match allowed MIME types
        $validator = new Validator(['upload' => 'jpeg_file_value'], ['upload' => 'mimetypes:application/pdf,application/msword']);

        // Override the attribute key to match our $_FILES key
        $reflectionClass = new \ReflectionClass($validator);
        $attributesProperty = $reflectionClass->getProperty('attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($validator, ['jpeg_file' => 'jpeg_file_value']);

        $this->assertTrue($validator->fails());

        // File with upload error
        $validator = new Validator(['upload' => 'error_file_value'], ['upload' => 'mimetypes:image/jpeg,image/png']);

        // Override the attribute key
        $attributesProperty->setValue($validator, ['error_file' => 'error_file_value']);

        $this->assertTrue($validator->fails());

        // No parameters provided
        $validator = new Validator(['upload' => 'jpeg_file_value'], ['upload' => 'mimetypes:']);

        // Override the attribute key
        $attributesProperty->setValue($validator, ['jpeg_file' => 'jpeg_file_value']);

        $this->assertTrue($validator->fails());

        // File not in $_FILES
        $validator = new Validator(['missing_file' => 'some_value'], ['missing_file' => 'mimetypes:image/jpeg,image/png']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error messages for mimetypes rule
     */
    public function testMimeTypesErrorMessage()
    {
        $validator = new Validator(['upload' => 'jpeg_file_value'], ['upload' => 'mimetypes:application/pdf,application/msword']);

        // Override the attribute key to match our $_FILES key
        $reflectionClass = new \ReflectionClass($validator);
        $attributesProperty = $reflectionClass->getProperty('attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($validator, ['jpeg_file' => 'jpeg_file_value']);

        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('jpeg_file', $errors);
        $this->assertEquals('The jpeg_file must be a file with MIME type: application/pdf, application/msword.', $errors['jpeg_file'][0]);
    }
}
