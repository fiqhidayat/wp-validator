<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class InRuleTest extends TestCase
{
    /**
     * Test that the in rule passes when value is in the allowed list
     */
    public function testInRulePasses()
    {
        // String in list
        $validator = new Validator(['status' => 'active'], ['status' => 'in:active,pending,inactive']);
        $this->assertTrue($validator->passes());

        // Number in list (as integer)
        $validator = new Validator(['priority' => 2], ['priority' => 'in:1,2,3']);
        $this->assertTrue($validator->passes());

        // Number in list (as string)
        $validator = new Validator(['priority' => '2'], ['priority' => 'in:1,2,3']);
        $this->assertTrue($validator->passes());

        // Case sensitive
        $validator = new Validator(['status' => 'ACTIVE'], ['status' => 'in:ACTIVE,PENDING,INACTIVE']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['status' => null], ['status' => 'nullable|in:active,pending,inactive']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the in rule fails when value is not in the allowed list
     */
    public function testInRuleFails()
    {
        // String not in list
        $validator = new Validator(['status' => 'archived'], ['status' => 'in:active,pending,inactive']);
        $this->assertTrue($validator->fails());

        // Case sensitive (different case fails)
        $validator = new Validator(['status' => 'Active'], ['status' => 'in:active,pending,inactive']);
        $this->assertTrue($validator->fails());

        // Number not in list
        $validator = new Validator(['priority' => 4], ['priority' => 'in:1,2,3']);
        $this->assertTrue($validator->fails());

        // Empty string
        $validator = new Validator(['status' => ''], ['status' => 'in:active,pending,inactive']);
        $this->assertTrue($validator->fails());

        // Array
        $validator = new Validator(['status' => ['active']], ['status' => 'in:active,pending,inactive']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testInErrorMessage()
    {
        $validator = new Validator(['status' => 'archived'], ['status' => 'in:active,pending,inactive']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('status', $errors);
        $this->assertStringContainsString('status must be one of', $errors['status'][0]);
        $this->assertStringContainsString('active, pending, inactive', $errors['status'][0]);
    }
}
