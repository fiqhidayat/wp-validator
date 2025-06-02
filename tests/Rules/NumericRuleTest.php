<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class NumericRuleTest extends TestCase
{
    /**
     * Test that the numeric rule passes with valid data
     */
    public function testNumericRulePasses()
    {
        // Integer
        $validator = new Validator(['value' => 123], ['value' => 'numeric']);
        $this->assertTrue($validator->passes());

        // Float
        $validator = new Validator(['value' => 123.45], ['value' => 'numeric']);
        $this->assertTrue($validator->passes());

        // Numeric string (integer)
        $validator = new Validator(['value' => '123'], ['value' => 'numeric']);
        $this->assertTrue($validator->passes());

        // Numeric string (float)
        $validator = new Validator(['value' => '123.45'], ['value' => 'numeric']);
        $this->assertTrue($validator->passes());

        // Negative number
        $validator = new Validator(['value' => -123], ['value' => 'numeric']);
        $this->assertTrue($validator->passes());

        // Zero
        $validator = new Validator(['value' => 0], ['value' => 'numeric']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['value' => null], ['value' => 'nullable|numeric']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the numeric rule fails with invalid data
     */
    public function testNumericRuleFails()
    {
        // Text
        $validator = new Validator(['value' => 'abc'], ['value' => 'numeric']);
        $this->assertTrue($validator->fails());

        // Alphanumeric
        $validator = new Validator(['value' => '123abc'], ['value' => 'numeric']);
        $this->assertTrue($validator->fails());

        // Array
        $validator = new Validator(['value' => [1, 2, 3]], ['value' => 'numeric']);
        $this->assertTrue($validator->fails());

        // Object
        $validator = new Validator(['value' => (object)[]], ['value' => 'numeric']);
        $this->assertTrue($validator->fails());

        // Boolean
        $validator = new Validator(['value' => true], ['value' => 'numeric']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testNumericErrorMessage()
    {
        $validator = new Validator(['value' => 'abc'], ['value' => 'numeric']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('value', $errors);
        $this->assertStringContainsString('must be a number', $errors['value'][0]);
    }
}
