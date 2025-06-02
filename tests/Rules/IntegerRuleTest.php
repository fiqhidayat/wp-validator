<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class IntegerRuleTest extends TestCase
{
    /**
     * Test that the integer rule passes with valid data
     */
    public function testIntegerRulePasses()
    {
        // Integer
        $validator = new Validator(['value' => 123], ['value' => 'integer']);
        $this->assertTrue($validator->passes());

        // Integer as string
        $validator = new Validator(['value' => '123'], ['value' => 'integer']);
        $this->assertTrue($validator->passes());

        // Zero
        $validator = new Validator(['value' => 0], ['value' => 'integer']);
        $this->assertTrue($validator->passes());

        // Negative integer
        $validator = new Validator(['value' => -123], ['value' => 'integer']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['value' => null], ['value' => 'nullable|integer']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the integer rule fails with invalid data
     */
    public function testIntegerRuleFails()
    {
        // Float
        $validator = new Validator(['value' => 123.45], ['value' => 'integer']);
        $this->assertTrue($validator->fails());

        // Float as string
        $validator = new Validator(['value' => '123.45'], ['value' => 'integer']);
        $this->assertTrue($validator->fails());

        // Text
        $validator = new Validator(['value' => 'abc'], ['value' => 'integer']);
        $this->assertTrue($validator->fails());

        // Alphanumeric
        $validator = new Validator(['value' => '123abc'], ['value' => 'integer']);
        $this->assertTrue($validator->fails());

        // Array
        $validator = new Validator(['value' => [1, 2, 3]], ['value' => 'integer']);
        $this->assertTrue($validator->fails());

        // Object
        $validator = new Validator(['value' => (object)[]], ['value' => 'integer']);
        $this->assertTrue($validator->fails());

        // Boolean
        $validator = new Validator(['value' => true], ['value' => 'integer']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testIntegerErrorMessage()
    {
        $validator = new Validator(['value' => 123.45], ['value' => 'integer']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('value', $errors);
        $this->assertStringContainsString('value must be an integer', $errors['value'][0]);
    }
}
