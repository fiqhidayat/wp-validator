<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class DigitsRuleTest extends TestCase
{
    /**
     * Test that the digits rule passes with valid digit strings of exact length
     */
    public function testDigitsRulePasses()
    {
        // Exactly 5 digits
        $validator = new Validator(['zip' => '12345'], ['zip' => 'digits:5']);
        $this->assertTrue($validator->passes());

        // Exactly 10 digits
        $validator = new Validator(['phone' => '1234567890'], ['phone' => 'digits:10']);
        $this->assertTrue($validator->passes());

        // Single digit
        $validator = new Validator(['digit' => '7'], ['digit' => 'digits:1']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['zip' => null], ['zip' => 'nullable|digits:5']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the digits rule fails with invalid values
     */
    public function testDigitsRuleFails()
    {
        // Wrong length
        $validator = new Validator(['zip' => '123456'], ['zip' => 'digits:5']);
        $this->assertTrue($validator->fails());

        // Too short
        $validator = new Validator(['zip' => '1234'], ['zip' => 'digits:5']);
        $this->assertTrue($validator->fails());

        // Contains non-digits
        $validator = new Validator(['zip' => '1234a'], ['zip' => 'digits:5']);
        $this->assertTrue($validator->fails());

        // Contains decimal point
        $validator = new Validator(['zip' => '123.45'], ['zip' => 'digits:5']);
        $this->assertTrue($validator->fails());

        // Integer instead of string
        $validator = new Validator(['zip' => 12345], ['zip' => 'digits:5']);
        $this->assertTrue($validator->fails());

        // Empty string
        $validator = new Validator(['zip' => ''], ['zip' => 'digits:5']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testDigitsErrorMessage()
    {
        $validator = new Validator(['zip' => '1234'], ['zip' => 'digits:5']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('zip', $errors);
        $this->assertStringContainsString('5 digits', $errors['zip'][0]);
    }
}
