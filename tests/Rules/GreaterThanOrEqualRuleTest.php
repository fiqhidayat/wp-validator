<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class GreaterThanOrEqualRuleTest extends TestCase
{
    /**
     * Test that the gte rule passes when the value is greater than or equal to the specified field
     */
    public function testGreaterThanOrEqualRulePasses()
    {
        // Test with greater than
        $validator = new Validator(
            ['value' => 10, 'min_value' => 5],
            ['value' => 'numeric|gte:min_value']
        );
        $this->assertTrue($validator->passes());

        // Test with equal values
        $validator = new Validator(
            ['value' => 10, 'min_value' => 10],
            ['value' => 'numeric|gte:min_value']
        );
        $this->assertTrue($validator->passes());

        // Test with decimal values
        $validator = new Validator(
            ['price' => 19.99, 'min_price' => 19.99],
            ['price' => 'numeric|gte:min_price']
        );
        $this->assertTrue($validator->passes());

        // Test with zero and negative numbers
        $validator = new Validator(
            ['temperature' => -10, 'min_temp' => -10],
            ['temperature' => 'numeric|gte:min_temp']
        );
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the gte rule fails when the value is not greater than or equal to the specified field
     */
    public function testGreaterThanOrEqualRuleFails()
    {
        // Less than
        $validator = new Validator(
            ['value' => 5, 'min_value' => 10],
            ['value' => 'numeric|gte:min_value']
        );
        $this->assertTrue($validator->fails());

        // Missing parameter
        $validator = new Validator(
            ['value' => 10],
            ['value' => 'numeric|gte:']
        );
        $this->assertTrue($validator->fails());

        // Non-numeric values
        $validator = new Validator(
            ['value' => 'abc', 'min_value' => 5],
            ['value' => 'gte:min_value']
        );
        $this->assertTrue($validator->fails());

        $validator = new Validator(
            ['value' => 10, 'min_value' => 'abc'],
            ['value' => 'gte:min_value']
        );
        $this->assertTrue($validator->fails());
    }

    /**
     * Test the error message for the gte rule
     */
    public function testGreaterThanOrEqualErrorMessage()
    {
        $validator = new Validator(
            ['value' => 5, 'min_value' => 10],
            ['value' => 'gte:min_value']
        );
        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('value', $errors);
        $this->assertEquals('The value must be greater than or equal to min value.', $errors['value'][0]);
    }
}
