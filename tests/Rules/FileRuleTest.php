<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class FileRuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Set up mock for PHP superglobals
        global $_FILES;

        // Mock $_FILES for testing
        $_FILES = [
            'valid_file' => [
                'name' => 'test.txt',
                'type' => 'text/plain',
                'size' => 1024,
                'tmp_name' => '/tmp/phpXXXXXX',
                'error' => UPLOAD_ERR_OK
            ],
            'error_file' => [
                'name' => 'error.txt',
                'type' => 'text/plain',
                'size' => 1024,
                'tmp_name' => '/tmp/phpXXXXXX',
                'error' => UPLOAD_ERR_PARTIAL
            ]
        ];

        // Mock the is_uploaded_file function
        if (!function_exists('is_uploaded_file')) {
            function is_uploaded_file($filename)
            {
                // Simply check if the path contains 'phpXXXXXX'
                return strpos($filename, 'phpXXXXXX') !== false;
            }
        }
    }

    /**
     * Test that the file rule passes with valid file uploads
     */
    public function testFileRulePasses()
    {
        // Valid file upload
        $validator = new Validator(['upload' => 'valid_file_value'], ['upload' => 'file']);

        // Override the attribute key to match our $_FILES key
        $reflectionClass = new \ReflectionClass($validator);
        $attributesProperty = $reflectionClass->getProperty('attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($validator, ['valid_file' => 'valid_file_value']);

        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the file rule fails with invalid file uploads
     */
    public function testFileRuleFails()
    {
        // File with upload error
        $validator = new Validator(['upload' => 'error_file_value'], ['upload' => 'file']);

        // Override the attribute key to match our $_FILES key
        $reflectionClass = new \ReflectionClass($validator);
        $attributesProperty = $reflectionClass->getProperty('attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($validator, ['error_file' => 'error_file_value']);

        $this->assertTrue($validator->fails());

        // File not in $_FILES
        $validator = new Validator(['missing_file' => 'some_value'], ['missing_file' => 'file']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error messages for file rule
     */
    public function testFileErrorMessage()
    {
        $validator = new Validator(['missing_file' => 'some_value'], ['missing_file' => 'file']);
        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('missing_file', $errors);
        $this->assertEquals('The missing_file must be a valid file upload.', $errors['missing_file'][0]);
    }
}
