<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class MimesRuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Set up mock for PHP superglobals and functions
        global $_FILES;

        // Mock $_FILES for testing
        $_FILES = [
            'jpg_file' => [
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
     * Test that the mimes rule passes with valid file types
     */
    public function testMimesRulePasses()
    {
        // JPG file matches jpg extension
        $validator = new Validator(['upload' => 'jpg_file_value'], ['upload' => 'mimes:jpg,png']);

        // Override the attribute key to match our $_FILES key
        $reflectionClass = new \ReflectionClass($validator);
        $attributesProperty = $reflectionClass->getProperty('attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($validator, ['jpg_file' => 'jpg_file_value']);

        $this->assertTrue($validator->passes());

        // PDF file matches pdf extension
        $validator = new Validator(['upload' => 'pdf_file_value'], ['upload' => 'mimes:pdf,doc']);

        // Override the attribute key to match our $_FILES key
        $attributesProperty->setValue($validator, ['pdf_file' => 'pdf_file_value']);

        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the mimes rule fails with invalid file types
     */
    public function testMimesRuleFails()
    {
        // JPG file doesn't match allowed types
        $validator = new Validator(['upload' => 'jpg_file_value'], ['upload' => 'mimes:pdf,doc']);

        // Override the attribute key to match our $_FILES key
        $reflectionClass = new \ReflectionClass($validator);
        $attributesProperty = $reflectionClass->getProperty('attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($validator, ['jpg_file' => 'jpg_file_value']);

        $this->assertTrue($validator->fails());

        // File with upload error
        $validator = new Validator(['upload' => 'error_file_value'], ['upload' => 'mimes:jpg,png']);

        // Override the attribute key
        $attributesProperty->setValue($validator, ['error_file' => 'error_file_value']);

        $this->assertTrue($validator->fails());

        // No parameters provided
        $validator = new Validator(['upload' => 'jpg_file_value'], ['upload' => 'mimes:']);

        // Override the attribute key
        $attributesProperty->setValue($validator, ['jpg_file' => 'jpg_file_value']);

        $this->assertTrue($validator->fails());

        // File not in $_FILES
        $validator = new Validator(['missing_file' => 'some_value'], ['missing_file' => 'mimes:jpg,png']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error messages for mimes rule
     */
    public function testMimesErrorMessage()
    {
        $validator = new Validator(['upload' => 'jpg_file_value'], ['upload' => 'mimes:pdf,doc']);

        // Override the attribute key to match our $_FILES key
        $reflectionClass = new \ReflectionClass($validator);
        $attributesProperty = $reflectionClass->getProperty('attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($validator, ['jpg_file' => 'jpg_file_value']);

        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('jpg_file', $errors);
        $this->assertEquals('The jpg_file must be a file of type: pdf, doc.', $errors['jpg_file'][0]);
    }
}
