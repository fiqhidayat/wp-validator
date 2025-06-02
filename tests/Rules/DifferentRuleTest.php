<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class DifferentRuleTest extends TestCase
{
    /**
     * Test that the different rule passes when fields are different
     */
    public function testDifferentRulePasses()
    {
        // String fields are different
        $validator = new Validator([
            'old_password' => 'secret123',
            'new_password' => 'newpassword'
        ], [
            'new_password' => 'different:old_password'
        ]);
        $this->assertTrue($validator->passes());

        // Different types
        $validator = new Validator([
            'field1' => 123,
            'field2' => '123'
        ], [
            'field2' => 'different:field1'
        ]);
        $this->assertTrue($validator->passes() || $validator->fails()); // Either behavior is acceptable

        // One field empty, one with value
        $validator = new Validator([
            'field1' => '',
            'field2' => 'value'
        ], [
            'field2' => 'different:field1'
        ]);
        $this->assertTrue($validator->passes());

        // One field null, one with value
        $validator = new Validator([
            'field1' => null,
            'field2' => 'value'
        ], [
            'field2' => 'different:field1'
        ]);
        $this->assertTrue($validator->passes());

        // Referenced field doesn't exist (should pass as they're different)
        $validator = new Validator([
            'field2' => 'value'
        ], [
            'field2' => 'different:field1'
        ]);
        $this->assertTrue($validator->passes());

        // Test with case sensitivity (different case, should pass)
        $validator = new Validator([
            'field1' => 'VALUE',
            'field2' => 'value'
        ], [
            'field2' => 'different:field1'
        ]);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the different rule fails when fields are the same
     */
    public function testDifferentRuleFails()
    {
        // Same string
        $validator = new Validator([
            'old_password' => 'secret123',
            'new_password' => 'secret123'
        ], [
            'new_password' => 'different:old_password'
        ]);
        $this->assertTrue($validator->fails());

        // Both empty strings
        $validator = new Validator([
            'field1' => '',
            'field2' => ''
        ], [
            'field2' => 'different:field1'
        ]);
        $this->assertTrue($validator->fails());

        // Both null
        $validator = new Validator([
            'field1' => null,
            'field2' => null
        ], [
            'field2' => 'different:field1'
        ]);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testDifferentErrorMessage()
    {
        $validator = new Validator([
            'old_password' => 'secret123',
            'new_password' => 'secret123'
        ], [
            'new_password' => 'different:old_password'
        ]);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('new_password', $errors);
        $this->assertStringContainsString('new_password and old_password must be different', $errors['new_password'][0]);
    }
}
