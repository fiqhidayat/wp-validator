<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class MaxRuleTest extends TestCase
{
    /**
     * Test that the max rule passes with valid string lengths
     */
    public function testMaxRulePassesWithStrings()
    {
        // String exactly maximum length
        $validator = new Validator(['name' => 'John'], ['name' => 'max:4']);
        $this->assertTrue($validator->passes());

        // String shorter than maximum
        $validator = new Validator(['name' => 'Jo'], ['name' => 'max:4']);
        $this->assertTrue($validator->passes());

        // Empty string
        $validator = new Validator(['name' => ''], ['name' => 'max:4']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['name' => null], ['name' => 'nullable|max:4']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the max rule passes with valid numeric values
     */
    public function testMaxRulePassesWithNumbers()
    {
        // Number exactly maximum
        $validator = new Validator(['age' => 18], ['age' => 'max:18']);
        $this->assertTrue($validator->passes());

        // Number below maximum
        $validator = new Validator(['age' => 16], ['age' => 'max:18']);
        $this->assertTrue($validator->passes());

        // String number
        $validator = new Validator(['age' => '16'], ['age' => 'max:18']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the max rule passes with arrays
     */
    public function testMaxRulePassesWithArrays()
    {
        // Array with exactly max items
        $validator = new Validator(['items' => [1, 2, 3]], ['items' => 'array|max:3']);
        $this->assertTrue($validator->passes());

        // Array with fewer than max items
        $validator = new Validator(['items' => [1, 2]], ['items' => 'array|max:3']);
        $this->assertTrue($validator->passes());

        // Empty array
        $validator = new Validator(['items' => []], ['items' => 'array|max:3']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the max rule fails with invalid strings
     */
    public function testMaxRuleFailsWithStrings()
    {
        // String longer than maximum
        $validator = new Validator(['name' => 'Jonathan'], ['name' => 'max:5']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test that the max rule fails with invalid numbers
     */
    public function testMaxRuleFailsWithNumbers()
    {
        // Number above maximum
        $validator = new Validator(['age' => 21], ['age' => 'max:18']);
        $this->assertTrue($validator->fails());

        // String number above maximum
        $validator = new Validator(['age' => '21'], ['age' => 'max:18']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test that the max rule fails with arrays
     */
    public function testMaxRuleFailsWithArrays()
    {
        // Array with more items than maximum
        $validator = new Validator(['items' => [1, 2, 3, 4]], ['items' => 'array|max:3']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testMaxErrorMessage()
    {
        // For strings
        $validator = new Validator(['name' => 'Jonathan'], ['name' => 'max:5']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('name', $errors);
        $this->assertStringContainsString('name may not be greater than 5', $errors['name'][0]);

        // For numbers
        $validator = new Validator(['age' => 21], ['age' => 'max:18']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('age', $errors);
        $this->assertStringContainsString('age may not be greater than 18', $errors['age'][0]);
    }
}
