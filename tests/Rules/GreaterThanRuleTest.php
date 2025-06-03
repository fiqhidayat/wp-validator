<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class GreaterThanRuleTest extends TestCase
{
    /**
     * Test that the gt rule passes when the value is greater than the specified field
     */
    public function testGreaterThanRulePasses()
    {
        // Test with numeric values
        $validator = new Validator(
            ['value' => 10, 'min_value' => 5],
            ['value' => 'numeric|gt:min_value']
        );
        $this->assertTrue($validator->passes());

        // Test with decimal values
        $validator = new Validator(
            ['price' => 19.99, 'min_price' => 9.99],
            ['price' => 'numeric|gt:min_price']
        );
        $this->assertTrue($validator->passes());

        // Test with zero and negative numbers
        $validator = new Validator(
            ['temperature' => 0, 'min_temp' => -10],
            ['temperature' => 'numeric|gt:min_temp']
        );
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the gt rule fails when the value is not greater than the specified field
     */
    public function testGreaterThanRuleFails()
    {
        // Equal values
        $validator = new Validator(
            ['value' => 10, 'other_value' => 10],
            ['value' => 'numeric|gt:other_value']
        );
        $this->assertTrue($validator->fails());

        // Less than
        $validator = new Validator(
            ['value' => 5, 'max_value' => 10],
            ['value' => 'numeric|gt:max_value']
        );
        $this->assertTrue($validator->fails());

        // Missing parameter
        $validator = new Validator(
            ['value' => 10],
            ['value' => 'numeric|gt:']
        );
        $this->assertTrue($validator->fails());

        // Non-numeric values
        $validator = new Validator(
            ['value' => 'abc', 'other_value' => 5],
            ['value' => 'gt:other_value']
        );
        $this->assertTrue($validator->fails());

        $validator = new Validator(
            ['value' => 10, 'other_value' => 'abc'],
            ['value' => 'gt:other_value']
        );
        $this->assertTrue($validator->fails());
    }

    /**
     * Test the error message for the gt rule
     */
    public function testGreaterThanErrorMessage()
    {
        $validator = new Validator(
            ['value' => 5, 'min_value' => 10],
            ['value' => 'gt:min_value']
        );
        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('value', $errors);
        $this->assertEquals('The value must be greater than min value.', $errors['value'][0]);
    }
}
