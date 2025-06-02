<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class StringRuleTest extends TestCase
{
    /**
     * Test that the string rule passes with valid data
     */
    public function testStringRulePasses()
    {
        // Empty string
        $validator = new Validator(['text' => ''], ['text' => 'string']);
        $this->assertTrue($validator->passes());

        // Normal string
        $validator = new Validator(['text' => 'Hello World'], ['text' => 'string']);
        $this->assertTrue($validator->passes());

        // Numeric string
        $validator = new Validator(['text' => '12345'], ['text' => 'string']);
        $this->assertTrue($validator->passes());

        // String with special characters
        $validator = new Validator(['text' => 'Hello@World!'], ['text' => 'string']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['text' => null], ['text' => 'nullable|string']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the string rule fails with invalid data
     */
    public function testStringRuleFails()
    {
        // Integer
        $validator = new Validator(['text' => 123], ['text' => 'string']);
        $this->assertTrue($validator->fails());

        // Array
        $validator = new Validator(['text' => ['a', 'b', 'c']], ['text' => 'string']);
        $this->assertTrue($validator->fails());

        // Object
        $validator = new Validator(['text' => (object)['prop' => 'value']], ['text' => 'string']);
        $this->assertTrue($validator->fails());

        // Boolean
        $validator = new Validator(['text' => true], ['text' => 'string']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testStringErrorMessage()
    {
        $validator = new Validator(['text' => 123], ['text' => 'string']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('text', $errors);
        $this->assertStringContainsString('text must be a string', $errors['text'][0]);
    }
}
