<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class LessThanOrEqualRuleTest extends TestCase
{
    /**
     * Test that the lte rule passes when the value is less than or equal to the specified field
     */
    public function testLessThanOrEqualRulePasses()
    {
        // Test with less than
        $validator = new Validator(
            ['value' => 5, 'max_value' => 10],
            ['value' => 'numeric|lte:max_value']
        );
        $this->assertTrue($validator->passes());

        // Test with equal values
        $validator = new Validator(
            ['value' => 10, 'max_value' => 10],
            ['value' => 'numeric|lte:max_value']
        );
        $this->assertTrue($validator->passes());

        // Test with decimal values
        $validator = new Validator(
            ['price' => 19.99, 'max_price' => 19.99],
            ['price' => 'numeric|lte:max_price']
        );
        $this->assertTrue($validator->passes());

        // Test with zero and negative numbers
        $validator = new Validator(
            ['temperature' => -10, 'max_temp' => -10],
            ['temperature' => 'numeric|lte:max_temp']
        );
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the lte rule fails when the value is not less than or equal to the specified field
     */
    public function testLessThanOrEqualRuleFails()
    {
        // Greater than
        $validator = new Validator(
            ['value' => 15, 'max_value' => 10],
            ['value' => 'numeric|lte:max_value']
        );
        $this->assertTrue($validator->fails());

        // Missing parameter
        $validator = new Validator(
            ['value' => 10],
            ['value' => 'numeric|lte:']
        );
        $this->assertTrue($validator->fails());

        // Non-numeric values
        $validator = new Validator(
            ['value' => 'abc', 'max_value' => 5],
            ['value' => 'lte:max_value']
        );
        $this->assertTrue($validator->fails());

        $validator = new Validator(
            ['value' => 10, 'max_value' => 'abc'],
            ['value' => 'lte:max_value']
        );
        $this->assertTrue($validator->fails());
    }

    /**
     * Test the error message for the lte rule
     */
    public function testLessThanOrEqualErrorMessage()
    {
        $validator = new Validator(
            ['value' => 15, 'max_value' => 10],
            ['value' => 'lte:max_value']
        );
        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('value', $errors);
        $this->assertEquals('The value must be less than or equal to max value.', $errors['value'][0]);
    }
}
