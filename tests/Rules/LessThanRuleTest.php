<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class LessThanRuleTest extends TestCase
{
    /**
     * Test that the lt rule passes when the value is less than the specified field
     */
    public function testLessThanRulePasses()
    {
        // Test with numeric values
        $validator = new Validator(
            ['value' => 5, 'max_value' => 10],
            ['value' => 'numeric|lt:max_value']
        );
        $this->assertTrue($validator->passes());

        // Test with decimal values
        $validator = new Validator(
            ['price' => 9.99, 'max_price' => 19.99],
            ['price' => 'numeric|lt:max_price']
        );
        $this->assertTrue($validator->passes());

        // Test with zero and negative numbers
        $validator = new Validator(
            ['temperature' => -10, 'max_temp' => 0],
            ['temperature' => 'numeric|lt:max_temp']
        );
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the lt rule fails when the value is not less than the specified field
     */
    public function testLessThanRuleFails()
    {
        // Equal values
        $validator = new Validator(
            ['value' => 10, 'max_value' => 10],
            ['value' => 'numeric|lt:max_value']
        );
        $this->assertTrue($validator->fails());

        // Greater than
        $validator = new Validator(
            ['value' => 15, 'max_value' => 10],
            ['value' => 'numeric|lt:max_value']
        );
        $this->assertTrue($validator->fails());

        // Missing parameter
        $validator = new Validator(
            ['value' => 10],
            ['value' => 'numeric|lt:']
        );
        $this->assertTrue($validator->fails());

        // Non-numeric values
        $validator = new Validator(
            ['value' => 'abc', 'max_value' => 5],
            ['value' => 'lt:max_value']
        );
        $this->assertTrue($validator->fails());

        $validator = new Validator(
            ['value' => 10, 'max_value' => 'abc'],
            ['value' => 'lt:max_value']
        );
        $this->assertTrue($validator->fails());
    }

    /**
     * Test the error message for the lt rule
     */
    public function testLessThanErrorMessage()
    {
        $validator = new Validator(
            ['value' => 15, 'max_value' => 10],
            ['value' => 'lt:max_value']
        );
        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('value', $errors);
        $this->assertEquals('The value must be less than max value.', $errors['value'][0]);
    }
}
