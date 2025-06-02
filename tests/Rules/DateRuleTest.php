<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class DateRuleTest extends TestCase
{
    /**
     * Test that the date rule passes with valid dates
     */
    public function testDateRulePasses()
    {
        // ISO format
        $validator = new Validator(['date' => '2023-01-15'], ['date' => 'date']);
        $this->assertTrue($validator->passes());

        // ISO format with time
        $validator = new Validator(['date' => '2023-01-15 14:30:00'], ['date' => 'date']);
        $this->assertTrue($validator->passes());

        // ISO format with T and Z
        $validator = new Validator(['date' => '2023-01-15T14:30:00Z'], ['date' => 'date']);
        $this->assertTrue($validator->passes());

        // US format
        $validator = new Validator(['date' => '01/15/2023'], ['date' => 'date']);
        $this->assertTrue($validator->passes());

        // Month and year only
        $validator = new Validator(['date' => '2023-01'], ['date' => 'date']);
        $this->assertTrue($validator->passes());

        // Timestamp
        $validator = new Validator(['date' => time()], ['date' => 'date']);
        $this->assertTrue($validator->passes());

        // DateTime object
        $validator = new Validator(['date' => new \DateTime()], ['date' => 'date']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['date' => null], ['date' => 'nullable|date']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the date rule fails with invalid dates
     */
    public function testDateRuleFails()
    {
        // Random string
        $validator = new Validator(['date' => 'not a date'], ['date' => 'date']);
        $this->assertTrue($validator->fails());

        // Invalid date
        $validator = new Validator(['date' => '2023-13-45'], ['date' => 'date']);
        $this->assertTrue($validator->fails());

        // Invalid format
        $validator = new Validator(['date' => '15-2023-01'], ['date' => 'date']);
        $this->assertTrue($validator->fails());

        // Empty string
        $validator = new Validator(['date' => ''], ['date' => 'date']);
        $this->assertTrue($validator->fails());

        // Array
        $validator = new Validator(['date' => ['2023-01-15']], ['date' => 'date']);
        $this->assertTrue($validator->fails());

        // Boolean
        $validator = new Validator(['date' => true], ['date' => 'date']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testDateErrorMessage()
    {
        $validator = new Validator(['date' => 'not a date'], ['date' => 'date']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('date', $errors);
        $this->assertStringContainsString('date is not a valid date', $errors['date'][0]);
    }
}
