<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class DimensionsRuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Set up mock for PHP superglobals and functions
        global $_FILES;

        // Mock $_FILES for testing
        $_FILES = [
            'square_image' => [
                'name' => 'square.jpg',
                'type' => 'image/jpeg',
                'size' => 1024,
                'tmp_name' => '/tmp/square_image',
                'error' => UPLOAD_ERR_OK
            ],
            'wide_image' => [
                'name' => 'wide.jpg',
                'type' => 'image/jpeg',
                'size' => 2048,
                'tmp_name' => '/tmp/wide_image',
                'error' => UPLOAD_ERR_OK
            ],
            'tall_image' => [
                'name' => 'tall.jpg',
                'type' => 'image/jpeg',
                'size' => 2048,
                'tmp_name' => '/tmp/tall_image',
                'error' => UPLOAD_ERR_OK
            ],
            'error_file' => [
                'name' => 'error.jpg',
                'type' => 'image/jpeg',
                'size' => 1024,
                'tmp_name' => '/tmp/error_file',
                'error' => UPLOAD_ERR_PARTIAL
            ],
            'not_image' => [
                'name' => 'text.txt',
                'type' => 'text/plain',
                'size' => 512,
                'tmp_name' => '/tmp/not_image',
                'error' => UPLOAD_ERR_OK
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
                if (strpos($filename, 'square_image') !== false) {
                    // 200x200 square image
                    return [200, 200, IMAGETYPE_JPEG, 'height="200" width="200"', 'mime' => 'image/jpeg'];
                } else if (strpos($filename, 'wide_image') !== false) {
                    // 400x200 wide image
                    return [400, 200, IMAGETYPE_JPEG, 'height="200" width="400"', 'mime' => 'image/jpeg'];
                } else if (strpos($filename, 'tall_image') !== false) {
                    // 200x400 tall image
                    return [200, 400, IMAGETYPE_JPEG, 'height="400" width="200"', 'mime' => 'image/jpeg'];
                } else if (strpos($filename, 'not_image') !== false) {
                    return false; // Not an image
                }
                return false;
            }
        }
    }

    /**
     * Test that the dimensions rule passes with valid image dimensions
     */
    public function testDimensionsRulePasses()
    {
        // Exact width and height match
        $validator = new Validator(['image' => 'square_image_value'], ['image' => 'dimensions:width=200,height=200']);

        // Override the attribute key to match our $_FILES key
        $reflectionClass = new \ReflectionClass($validator);
        $attributesProperty = $reflectionClass->getProperty('attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($validator, ['square_image' => 'square_image_value']);

        $this->assertTrue($validator->passes());

        // Min width and min height
        $validator = new Validator(['image' => 'square_image_value'], ['image' => 'dimensions:min_width=100,min_height=100']);
        $attributesProperty->setValue($validator, ['square_image' => 'square_image_value']);
        $this->assertTrue($validator->passes());

        // Max width and max height
        $validator = new Validator(['image' => 'square_image_value'], ['image' => 'dimensions:max_width=300,max_height=300']);
        $attributesProperty->setValue($validator, ['square_image' => 'square_image_value']);
        $this->assertTrue($validator->passes());

        // Ratio 1:1 for square image
        $validator = new Validator(['image' => 'square_image_value'], ['image' => 'dimensions:ratio=1/1']);
        $attributesProperty->setValue($validator, ['square_image' => 'square_image_value']);
        $this->assertTrue($validator->passes());

        // Ratio 2:1 for wide image
        $validator = new Validator(['image' => 'wide_image_value'], ['image' => 'dimensions:ratio=2/1']);
        $attributesProperty->setValue($validator, ['wide_image' => 'wide_image_value']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the dimensions rule fails with invalid image dimensions
     */
    public function testDimensionsRuleFails()
    {
        // Wrong width
        $validator = new Validator(['image' => 'square_image_value'], ['image' => 'dimensions:width=300']);

        // Override the attribute key to match our $_FILES key
        $reflectionClass = new \ReflectionClass($validator);
        $attributesProperty = $reflectionClass->getProperty('attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($validator, ['square_image' => 'square_image_value']);

        $this->assertTrue($validator->fails());

        // Too small for min_width
        $validator = new Validator(['image' => 'square_image_value'], ['image' => 'dimensions:min_width=300']);
        $attributesProperty->setValue($validator, ['square_image' => 'square_image_value']);
        $this->assertTrue($validator->fails());

        // Too large for max_width
        $validator = new Validator(['image' => 'square_image_value'], ['image' => 'dimensions:max_width=100']);
        $attributesProperty->setValue($validator, ['square_image' => 'square_image_value']);
        $this->assertTrue($validator->fails());

        // Wrong ratio
        $validator = new Validator(['image' => 'wide_image_value'], ['image' => 'dimensions:ratio=1/1']);
        $attributesProperty->setValue($validator, ['wide_image' => 'wide_image_value']);
        $this->assertTrue($validator->fails());

        // File with upload error
        $validator = new Validator(['image' => 'error_file_value'], ['image' => 'dimensions:width=200,height=200']);
        $attributesProperty->setValue($validator, ['error_file' => 'error_file_value']);
        $this->assertTrue($validator->fails());

        // Not an image
        $validator = new Validator(['image' => 'not_image_value'], ['image' => 'dimensions:width=200,height=200']);
        $attributesProperty->setValue($validator, ['not_image' => 'not_image_value']);
        $this->assertTrue($validator->fails());

        // File not in $_FILES
        $validator = new Validator(['missing_file' => 'some_value'], ['missing_file' => 'dimensions:width=200,height=200']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error messages for dimensions rule
     */
    public function testDimensionsErrorMessage()
    {
        $validator = new Validator(['image' => 'square_image_value'], ['image' => 'dimensions:width=300,height=300']);

        // Override the attribute key to match our $_FILES key
        $reflectionClass = new \ReflectionClass($validator);
        $attributesProperty = $reflectionClass->getProperty('attributes');
        $attributesProperty->setAccessible(true);
        $attributesProperty->setValue($validator, ['square_image' => 'square_image_value']);

        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('square_image', $errors);
        $this->assertEquals('The square_image image dimensions are invalid. Required dimensions: width=300, height=300', $errors['square_image'][0]);
    }
}
