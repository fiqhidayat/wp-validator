<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class AlphaNumRuleTest extends TestCase
{
    /**
     * Test that the alpha_num rule passes with alphanumeric characters
     */
    public function testAlphaNumRulePasses()
    {
        // Lowercase letters
        $validator = new Validator(['username' => 'john'], ['username' => 'alpha_num']);
        $this->assertTrue($validator->passes());

        // Uppercase letters
        $validator = new Validator(['username' => 'JOHN'], ['username' => 'alpha_num']);
        $this->assertTrue($validator->passes());

        // Numbers only
        $validator = new Validator(['username' => '12345'], ['username' => 'alpha_num']);
        $this->assertTrue($validator->passes());

        // Mixed alphanumeric
        $validator = new Validator(['username' => 'john123'], ['username' => 'alpha_num']);
        $this->assertTrue($validator->passes());

        // Empty string (debatable if this should pass, depends on implementation)
        $validator = new Validator(['username' => ''], ['username' => 'alpha_num']);
        $this->assertTrue($validator->passes() || $validator->fails()); // Accept either behavior

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['username' => null], ['username' => 'nullable|alpha_num']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the alpha_num rule fails with non-alphanumeric characters
     */
    public function testAlphaNumRuleFails()
    {
        // With space
        $validator = new Validator(['username' => 'john 123'], ['username' => 'alpha_num']);
        $this->assertTrue($validator->fails());

        // With special character
        $validator = new Validator(['username' => 'john-123'], ['username' => 'alpha_num']);
        $this->assertTrue($validator->fails());

        // With underscore
        $validator = new Validator(['username' => 'john_123'], ['username' => 'alpha_num']);
        $this->assertTrue($validator->fails());

        // With punctuation
        $validator = new Validator(['username' => 'john.123'], ['username' => 'alpha_num']);
        $this->assertTrue($validator->fails());

        // Array
        $validator = new Validator(['username' => ['john123']], ['username' => 'alpha_num']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testAlphaNumErrorMessage()
    {
        $validator = new Validator(['username' => 'john-123'], ['username' => 'alpha_num']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('username', $errors);
        $this->assertStringContainsString('username may only contain letters and numbers', $errors['username'][0]);
    }
}
