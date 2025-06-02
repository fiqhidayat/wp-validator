<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class BooleanRuleTest extends TestCase
{
    /**
     * Test that the boolean rule passes with valid boolean values
     */
    public function testBooleanRulePasses()
    {
        // True
        $validator = new Validator(['active' => true], ['active' => 'boolean']);
        $this->assertTrue($validator->passes());

        // False
        $validator = new Validator(['active' => false], ['active' => 'boolean']);
        $this->assertTrue($validator->passes());

        // Integer 1
        $validator = new Validator(['active' => 1], ['active' => 'boolean']);
        $this->assertTrue($validator->passes());

        // Integer 0
        $validator = new Validator(['active' => 0], ['active' => 'boolean']);
        $this->assertTrue($validator->passes());

        // String "1"
        $validator = new Validator(['active' => "1"], ['active' => 'boolean']);
        $this->assertTrue($validator->passes());

        // String "0"
        $validator = new Validator(['active' => "0"], ['active' => 'boolean']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['active' => null], ['active' => 'nullable|boolean']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the boolean rule fails with invalid boolean values
     */
    public function testBooleanRuleFails()
    {
        // Integer other than 0,1
        $validator = new Validator(['active' => 2], ['active' => 'boolean']);
        $this->assertTrue($validator->fails());

        // String other than "0","1","true","false"
        $validator = new Validator(['active' => "yes"], ['active' => 'boolean']);
        $this->assertTrue($validator->fails());

        // Array
        $validator = new Validator(['active' => [true]], ['active' => 'boolean']);
        $this->assertTrue($validator->fails());

        // Object
        $validator = new Validator(['active' => (object)["value" => true]], ['active' => 'boolean']);
        $this->assertTrue($validator->fails());

        // Empty string
        $validator = new Validator(['active' => ""], ['active' => 'boolean']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testBooleanErrorMessage()
    {
        $validator = new Validator(['active' => "invalid"], ['active' => 'boolean']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('active', $errors);
        $this->assertStringContainsString('active field must be true or false', $errors['active'][0]);
    }
}
