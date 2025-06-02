<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class EmailRuleTest extends TestCase
{
    /**
     * Test that the email rule passes with valid email addresses
     */
    public function testEmailRulePasses()
    {
        // Simple email
        $validator = new Validator(['email' => 'test@example.com'], ['email' => 'email']);
        $this->assertTrue($validator->passes());

        // Email with subdomain
        $validator = new Validator(['email' => 'test@sub.example.com'], ['email' => 'email']);
        $this->assertTrue($validator->passes());

        // Email with plus sign
        $validator = new Validator(['email' => 'test+tag@example.com'], ['email' => 'email']);
        $this->assertTrue($validator->passes());

        // Email with numbers
        $validator = new Validator(['email' => 'test123@example.com'], ['email' => 'email']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['email' => null], ['email' => 'nullable|email']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the email rule fails with invalid email addresses
     */
    public function testEmailRuleFails()
    {
        // Missing @
        $validator = new Validator(['email' => 'testexample.com'], ['email' => 'email']);
        $this->assertTrue($validator->fails());

        // Missing domain
        $validator = new Validator(['email' => 'test@'], ['email' => 'email']);
        $this->assertTrue($validator->fails());

        // Special characters
        $validator = new Validator(['email' => 'test@example.com!'], ['email' => 'email']);
        $this->assertTrue($validator->fails());

        // Multiple @
        $validator = new Validator(['email' => 'test@example@com'], ['email' => 'email']);
        $this->assertTrue($validator->fails());

        // Just plain text
        $validator = new Validator(['email' => 'not an email'], ['email' => 'email']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testEmailErrorMessage()
    {
        $validator = new Validator(['email' => 'not-valid'], ['email' => 'email']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('email', $errors);
        $this->assertStringContainsString('email must be a valid email address', $errors['email'][0]);
    }
}
