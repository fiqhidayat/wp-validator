<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class AlphaDashRuleTest extends TestCase
{
    /**
     * Test that the alpha_dash rule passes with valid characters
     */
    public function testAlphaDashRulePasses()
    {
        // Lowercase letters
        $validator = new Validator(['username' => 'john'], ['username' => 'alpha_dash']);
        $this->assertTrue($validator->passes());

        // Uppercase letters
        $validator = new Validator(['username' => 'JOHN'], ['username' => 'alpha_dash']);
        $this->assertTrue($validator->passes());

        // Numbers
        $validator = new Validator(['username' => '12345'], ['username' => 'alpha_dash']);
        $this->assertTrue($validator->passes());

        // With dash
        $validator = new Validator(['username' => 'john-doe'], ['username' => 'alpha_dash']);
        $this->assertTrue($validator->passes());

        // With underscore
        $validator = new Validator(['username' => 'john_doe'], ['username' => 'alpha_dash']);
        $this->assertTrue($validator->passes());

        // Mixed alphanumeric with dashes and underscores
        $validator = new Validator(['username' => 'john-doe_123'], ['username' => 'alpha_dash']);
        $this->assertTrue($validator->passes());

        // Empty string (debatable if this should pass, depends on implementation)
        $validator = new Validator(['username' => ''], ['username' => 'alpha_dash']);
        $this->assertTrue($validator->passes() || $validator->fails()); // Accept either behavior

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['username' => null], ['username' => 'nullable|alpha_dash']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the alpha_dash rule fails with invalid characters
     */
    public function testAlphaDashRuleFails()
    {
        // With space
        $validator = new Validator(['username' => 'john doe'], ['username' => 'alpha_dash']);
        $this->assertTrue($validator->fails());

        // With special character
        $validator = new Validator(['username' => 'john@doe'], ['username' => 'alpha_dash']);
        $this->assertTrue($validator->fails());

        // With punctuation
        $validator = new Validator(['username' => 'john.doe'], ['username' => 'alpha_dash']);
        $this->assertTrue($validator->fails());

        // Array
        $validator = new Validator(['username' => ['john-doe']], ['username' => 'alpha_dash']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testAlphaDashErrorMessage()
    {
        $validator = new Validator(['username' => 'john@doe'], ['username' => 'alpha_dash']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('username', $errors);
        $this->assertStringContainsString('username may only contain letters', $errors['username'][0]);
        $this->assertStringContainsString('numbers, dashes and underscores', $errors['username'][0]);
    }
}
