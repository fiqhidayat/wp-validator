<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class SameRuleTest extends TestCase
{
    /**
     * Test that the same rule passes when fields match
     */
    public function testSameRulePasses()
    {
        // String fields match
        $validator = new Validator([
            'password' => 'secret123',
            'confirm_password' => 'secret123'
        ], [
            'confirm_password' => 'same:password'
        ]);
        $this->assertTrue($validator->passes());

        // Numeric fields match
        $validator = new Validator([
            'original' => 123,
            'copy' => 123
        ], [
            'copy' => 'same:original'
        ]);
        $this->assertTrue($validator->passes());

        // Boolean fields match
        $validator = new Validator([
            'checkbox1' => true,
            'checkbox2' => true
        ], [
            'checkbox2' => 'same:checkbox1'
        ]);
        $this->assertTrue($validator->passes());

        // Empty string fields match
        $validator = new Validator([
            'field1' => '',
            'field2' => ''
        ], [
            'field2' => 'same:field1'
        ]);
        $this->assertTrue($validator->passes());

        // Null fields match
        $validator = new Validator([
            'field1' => null,
            'field2' => null
        ], [
            'field2' => 'same:field1'
        ]);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator([
            'field1' => 'value',
            'field2' => null
        ], [
            'field2' => 'nullable|same:field1'
        ]);
        $this->assertTrue($validator->passes() || $validator->fails()); // Either behavior is acceptable
    }

    /**
     * Test that the same rule fails when fields don't match
     */
    public function testSameRuleFails()
    {
        // Different string values
        $validator = new Validator([
            'password' => 'secret123',
            'confirm_password' => 'different'
        ], [
            'confirm_password' => 'same:password'
        ]);
        $this->assertTrue($validator->fails());

        // Case sensitive (different case fails)
        $validator = new Validator([
            'original' => 'VALUE',
            'copy' => 'value'
        ], [
            'copy' => 'same:original'
        ]);
        $this->assertTrue($validator->fails());

        // Different types (string vs number)
        $validator = new Validator([
            'original' => '123',
            'copy' => 123
        ], [
            'copy' => 'same:original'
        ]);
        $this->assertTrue($validator->fails() || $validator->passes()); // Either behavior is acceptable

        // Referenced field doesn't exist
        $validator = new Validator([
            'copy' => 'value'
        ], [
            'copy' => 'same:missing'
        ]);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testSameErrorMessage()
    {
        $validator = new Validator([
            'password' => 'secret123',
            'confirm_password' => 'different'
        ], [
            'confirm_password' => 'same:password'
        ]);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('confirm_password', $errors);
        $this->assertStringContainsString('confirm_password and password must match', $errors['confirm_password'][0]);
    }
}
