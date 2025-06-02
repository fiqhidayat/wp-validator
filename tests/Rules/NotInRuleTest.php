<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class NotInRuleTest extends TestCase
{
    /**
     * Test that the not_in rule passes when value is not in the forbidden list
     */
    public function testNotInRulePasses()
    {
        // String not in list
        $validator = new Validator(['status' => 'active'], ['status' => 'not_in:pending,inactive,archived']);
        $this->assertTrue($validator->passes());

        // Number not in list
        $validator = new Validator(['priority' => 4], ['priority' => 'not_in:1,2,3']);
        $this->assertTrue($validator->passes());

        // Case sensitive
        $validator = new Validator(['status' => 'Active'], ['status' => 'not_in:active,pending,inactive']);
        $this->assertTrue($validator->passes());

        // Empty string
        $validator = new Validator(['status' => ''], ['status' => 'not_in:active,pending,inactive']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['status' => null], ['status' => 'nullable|not_in:active,pending,inactive']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the not_in rule fails when value is in the forbidden list
     */
    public function testNotInRuleFails()
    {
        // String in list
        $validator = new Validator(['status' => 'active'], ['status' => 'not_in:active,pending,inactive']);
        $this->assertTrue($validator->fails());

        // Number in list
        $validator = new Validator(['priority' => 2], ['priority' => 'not_in:1,2,3']);
        $this->assertTrue($validator->fails());

        // String number in list
        $validator = new Validator(['priority' => '2'], ['priority' => 'not_in:1,2,3']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testNotInErrorMessage()
    {
        $validator = new Validator(['status' => 'active'], ['status' => 'not_in:active,pending,inactive']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('status', $errors);
        $this->assertStringContainsString('status may not be one of', $errors['status'][0]);
        $this->assertStringContainsString('active, pending, inactive', $errors['status'][0]);
    }
}
