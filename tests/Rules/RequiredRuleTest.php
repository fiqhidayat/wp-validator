<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class RequiredRuleTest extends TestCase
{
    /**
     * Test that the required rule passes with valid data
     */
    public function testRequiredRulePasses()
    {
        // Test with string
        $validator = new Validator(['name' => 'John'], ['name' => 'required']);
        $this->assertTrue($validator->passes());

        // Test with number
        $validator = new Validator(['age' => 25], ['age' => 'required']);
        $this->assertTrue($validator->passes());

        // Test with array
        $validator = new Validator(['items' => ['a', 'b']], ['items' => 'required']);
        $this->assertTrue($validator->passes());

        // Test with boolean true
        $validator = new Validator(['active' => true], ['active' => 'required']);
        $this->assertTrue($validator->passes());

        // Test with zero (should pass, as it's not empty)
        $validator = new Validator(['count' => 0], ['count' => 'required']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the required rule fails with invalid data
     */
    public function testRequiredRuleFails()
    {
        // Test with empty string
        $validator = new Validator(['name' => ''], ['name' => 'required']);
        $this->assertTrue($validator->fails());

        // Test with null
        $validator = new Validator(['name' => null], ['name' => 'required']);
        $this->assertTrue($validator->fails());

        // Test with missing field
        $validator = new Validator([], ['name' => 'required']);
        $this->assertTrue($validator->fails());

        // Test with empty array
        $validator = new Validator(['items' => []], ['items' => 'required']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testRequiredErrorMessage()
    {
        $validator = new Validator(['name' => ''], ['name' => 'required']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('name', $errors);
        $this->assertStringContainsString('name field is required', $errors['name'][0]);
    }
}
