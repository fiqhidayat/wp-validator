<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class TimezoneRuleTest extends TestCase
{
    /**
     * Test that the timezone rule passes with valid timezone identifiers
     */
    public function testTimezoneRulePasses()
    {
        // Common timezone identifiers
        $validator = new Validator(['timezone' => 'UTC'], ['timezone' => 'timezone']);
        $this->assertTrue($validator->passes());

        $validator = new Validator(['timezone' => 'America/New_York'], ['timezone' => 'timezone']);
        $this->assertTrue($validator->passes());

        $validator = new Validator(['timezone' => 'Europe/London'], ['timezone' => 'timezone']);
        $this->assertTrue($validator->passes());

        $validator = new Validator(['timezone' => 'Asia/Tokyo'], ['timezone' => 'timezone']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['timezone' => null], ['timezone' => 'nullable|timezone']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the timezone rule fails with invalid timezone identifiers
     */
    public function testTimezoneRuleFails()
    {
        // Invalid timezone
        $validator = new Validator(['timezone' => 'Invalid/Timezone'], ['timezone' => 'timezone']);
        $this->assertTrue($validator->fails());

        // Non-string values
        $validator = new Validator(['timezone' => 123], ['timezone' => 'timezone']);
        $this->assertTrue($validator->fails());

        $validator = new Validator(['timezone' => []], ['timezone' => 'timezone']);
        $this->assertTrue($validator->fails());

        $validator = new Validator(['timezone' => true], ['timezone' => 'timezone']);
        $this->assertTrue($validator->fails());

        // Empty string
        $validator = new Validator(['timezone' => ''], ['timezone' => 'timezone']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error messages for timezone rule
     */
    public function testTimezoneErrorMessage()
    {
        $validator = new Validator(['timezone' => 'Invalid/Timezone'], ['timezone' => 'timezone']);
        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('timezone', $errors);
        $this->assertEquals('The timezone must be a valid timezone.', $errors['timezone'][0]);
    }
}
