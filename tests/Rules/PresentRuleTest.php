<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class PresentRuleTest extends TestCase
{
    /**
     * Test that the present rule passes when field is present
     */
    public function testPresentRulePasses()
    {
        // String value
        $validator = new Validator(['name' => 'John Doe'], ['name' => 'present']);
        $this->assertTrue($validator->passes());

        // Empty string (still present)
        $validator = new Validator(['name' => ''], ['name' => 'present']);
        $this->assertTrue($validator->passes());

        // Null value (still present)
        $validator = new Validator(['name' => null], ['name' => 'present']);
        $this->assertTrue($validator->passes());

        // Empty array (still present)
        $validator = new Validator(['items' => []], ['items' => 'present']);
        $this->assertTrue($validator->passes());

        // False value (still present)
        $validator = new Validator(['active' => false], ['active' => 'present']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the present rule fails when field is not present
     */
    public function testPresentRuleFails()
    {
        // Field not present
        $validator = new Validator([], ['name' => 'present']);
        $this->assertTrue($validator->fails());

        // Different field present
        $validator = new Validator(['email' => 'test@example.com'], ['name' => 'present']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error messages for present rule
     */
    public function testPresentErrorMessage()
    {
        $validator = new Validator([], ['name' => 'present']);
        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('name', $errors);
        $this->assertEquals('The name field must be present.', $errors['name'][0]);
    }
}
