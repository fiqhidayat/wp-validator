<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class DigitsBetweenRuleTest extends TestCase
{
    /**
     * Test that the digits_between rule passes with valid digit strings within specified length range
     */
    public function testDigitsBetweenRulePasses()
    {
        // Min length (3)
        $validator = new Validator(['code' => '123'], ['code' => 'digits_between:3,6']);
        $this->assertTrue($validator->passes());

        // Mid length (4)
        $validator = new Validator(['code' => '1234'], ['code' => 'digits_between:3,6']);
        $this->assertTrue($validator->passes());

        // Max length (6)
        $validator = new Validator(['code' => '123456'], ['code' => 'digits_between:3,6']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['code' => null], ['code' => 'nullable|digits_between:3,6']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the digits_between rule fails with invalid values
     */
    public function testDigitsBetweenRuleFails()
    {
        // Too short
        $validator = new Validator(['code' => '12'], ['code' => 'digits_between:3,6']);
        $this->assertTrue($validator->fails());

        // Too long
        $validator = new Validator(['code' => '1234567'], ['code' => 'digits_between:3,6']);
        $this->assertTrue($validator->fails());

        // Contains non-digits
        $validator = new Validator(['code' => '123a'], ['code' => 'digits_between:3,6']);
        $this->assertTrue($validator->fails());

        // Contains decimal point
        $validator = new Validator(['code' => '12.34'], ['code' => 'digits_between:3,6']);
        $this->assertTrue($validator->fails());

        // Missing parameters
        $validator = new Validator(['code' => '1234'], ['code' => 'digits_between:3']);
        $this->assertTrue($validator->fails());

        // Invalid parameters
        $validator = new Validator(['code' => '1234'], ['code' => 'digits_between:a,b']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error messages for digits_between rule
     */
    public function testDigitsBetweenErrorMessage()
    {
        $validator = new Validator(['code' => '12'], ['code' => 'digits_between:3,6']);
        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('code', $errors);
        $this->assertEquals('The code must be between 3 and 6 digits.', $errors['code'][0]);
    }
}
