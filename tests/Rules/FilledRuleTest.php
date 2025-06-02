<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class FilledRuleTest extends TestCase
{
    /**
     * Test that the filled rule passes when field is present and not empty
     */
    public function testFilledRulePasses()
    {
        // String value
        $validator = new Validator(['name' => 'John Doe'], ['name' => 'filled']);
        $this->assertTrue($validator->passes());

        // Numeric value
        $validator = new Validator(['age' => 25], ['age' => 'filled']);
        $this->assertTrue($validator->passes());

        // Array with items
        $validator = new Validator(['items' => [1, 2, 3]], ['items' => 'filled']);
        $this->assertTrue($validator->passes());

        // Boolean value
        $validator = new Validator(['active' => false], ['active' => 'filled']);
        $this->assertTrue($validator->passes());

        // Field not present at all (should pass since filled only validates when present)
        $validator = new Validator([], ['name' => 'filled']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the filled rule fails with empty values when field is present
     */
    public function testFilledRuleFails()
    {
        // Empty string
        $validator = new Validator(['name' => ''], ['name' => 'filled']);
        $this->assertTrue($validator->fails());

        // Whitespace string
        $validator = new Validator(['name' => '   '], ['name' => 'filled']);
        $this->assertTrue($validator->fails());

        // Null value
        $validator = new Validator(['name' => null], ['name' => 'filled']);
        $this->assertTrue($validator->fails());

        // Empty array
        $validator = new Validator(['items' => []], ['items' => 'filled']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error messages for filled rule
     */
    public function testFilledErrorMessage()
    {
        $validator = new Validator(['name' => ''], ['name' => 'filled']);
        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('name', $errors);
        $this->assertEquals('The name field must not be empty when it is present.', $errors['name'][0]);
    }
}
