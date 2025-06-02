<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class RegexRuleTest extends TestCase
{
    /**
     * Test that the regex rule passes when value matches the pattern
     */
    public function testRegexRulePasses()
    {
        // Simple pattern
        $validator = new Validator(['code' => 'ABC123'], ['code' => 'regex:/^[A-Z0-9]+$/']);
        $this->assertTrue($validator->passes());

        // Pattern with start/end anchors
        $validator = new Validator(['zip' => '12345'], ['zip' => 'regex:/^\d{5}$/']);
        $this->assertTrue($validator->passes());

        // Email-like pattern
        $validator = new Validator(['email' => 'test@example.com'], ['email' => 'regex:/^.+@.+\..+$/']);
        $this->assertTrue($validator->passes());

        // Pattern with optional parts
        $validator = new Validator(['phone' => '(123) 456-7890'], ['phone' => 'regex:/^\(\d{3}\)\s?\d{3}-\d{4}$/']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['code' => null], ['code' => 'nullable|regex:/^[A-Z0-9]+$/']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the regex rule fails when value does not match the pattern
     */
    public function testRegexRuleFails()
    {
        // Non-matching value
        $validator = new Validator(['code' => 'abc123'], ['code' => 'regex:/^[A-Z0-9]+$/']);
        $this->assertTrue($validator->fails());

        // Wrong format
        $validator = new Validator(['zip' => '1234'], ['zip' => 'regex:/^\d{5}$/']);
        $this->assertTrue($validator->fails());

        // Empty string
        $validator = new Validator(['code' => ''], ['code' => 'regex:/^[A-Z0-9]+$/']);
        $this->assertTrue($validator->fails());

        // Array
        $validator = new Validator(['code' => ['ABC123']], ['code' => 'regex:/^[A-Z0-9]+$/']);
        $this->assertTrue($validator->fails());

        // Number (not string)
        $validator = new Validator(['code' => 12345], ['code' => 'regex:/^\d{5}$/']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testRegexErrorMessage()
    {
        $validator = new Validator(['code' => 'abc123'], ['code' => 'regex:/^[A-Z0-9]+$/']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('code', $errors);
        $this->assertStringContainsString('code format is invalid', $errors['code'][0]);
    }
}
