<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class ConfirmedRuleTest extends TestCase
{
    /**
     * Test that the confirmed rule passes when confirmation field matches
     */
    public function testConfirmedRulePasses()
    {
        // Password with matching confirmation
        $validator = new Validator([
            'password' => 'secret123',
            'password_confirmation' => 'secret123'
        ], [
            'password' => 'confirmed'
        ]);
        $this->assertTrue($validator->passes());

        // Email with matching confirmation
        $validator = new Validator([
            'email' => 'test@example.com',
            'email_confirmation' => 'test@example.com'
        ], [
            'email' => 'confirmed'
        ]);
        $this->assertTrue($validator->passes());

        // Empty string with matching confirmation
        $validator = new Validator([
            'field' => '',
            'field_confirmation' => ''
        ], [
            'field' => 'confirmed'
        ]);
        $this->assertTrue($validator->passes());

        // Test with nullable and null with matching confirmation (should pass if nullable)
        $validator = new Validator([
            'password' => null,
            'password_confirmation' => null
        ], [
            'password' => 'nullable|confirmed'
        ]);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the confirmed rule fails when confirmation doesn't match
     */
    public function testConfirmedRuleFails()
    {
        // Non-matching confirmation
        $validator = new Validator([
            'password' => 'secret123',
            'password_confirmation' => 'different'
        ], [
            'password' => 'confirmed'
        ]);
        $this->assertTrue($validator->fails());

        // Missing confirmation field
        $validator = new Validator([
            'password' => 'secret123'
        ], [
            'password' => 'confirmed'
        ]);
        $this->assertTrue($validator->fails());

        // Case sensitive (different case fails)
        $validator = new Validator([
            'password' => 'Secret123',
            'password_confirmation' => 'secret123'
        ], [
            'password' => 'confirmed'
        ]);
        $this->assertTrue($validator->fails());

        // Main field null, confirmation not null
        $validator = new Validator([
            'password' => null,
            'password_confirmation' => 'secret123'
        ], [
            'password' => 'confirmed'
        ]);
        $this->assertTrue($validator->fails());

        // Main field not null, confirmation null
        $validator = new Validator([
            'password' => 'secret123',
            'password_confirmation' => null
        ], [
            'password' => 'confirmed'
        ]);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testConfirmedErrorMessage()
    {
        $validator = new Validator([
            'password' => 'secret123',
            'password_confirmation' => 'different'
        ], [
            'password' => 'confirmed'
        ]);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('password', $errors);
        $this->assertStringContainsString('password confirmation does not match', $errors['password'][0]);
    }
}
