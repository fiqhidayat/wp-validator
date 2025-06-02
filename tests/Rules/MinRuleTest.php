<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class MinRuleTest extends TestCase
{
    /**
     * Test that the min rule passes with valid string lengths
     */
    public function testMinRulePassesWithStrings()
    {
        // String exactly minimum length
        $validator = new Validator(['name' => 'John'], ['name' => 'min:4']);
        $this->assertTrue($validator->passes());

        // String longer than minimum
        $validator = new Validator(['name' => 'Jonathan'], ['name' => 'min:4']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the min rule passes with valid numeric values
     */
    public function testMinRulePassesWithNumbers()
    {
        // Number exactly minimum
        $validator = new Validator(['age' => 18], ['age' => 'min:18']);
        $this->assertTrue($validator->passes());

        // Number above minimum
        $validator = new Validator(['age' => 25], ['age' => 'min:18']);
        $this->assertTrue($validator->passes());

        // String number
        $validator = new Validator(['age' => '25'], ['age' => 'min:18']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['age' => null], ['age' => 'nullable|min:18']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the min rule passes with arrays
     */
    public function testMinRulePassesWithArrays()
    {
        // Array with exactly min items
        $validator = new Validator(['items' => [1, 2, 3]], ['items' => 'array|min:3']);
        $this->assertTrue($validator->passes());

        // Array with more than min items
        $validator = new Validator(['items' => [1, 2, 3, 4, 5]], ['items' => 'array|min:3']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the min rule fails with invalid strings
     */
    public function testMinRuleFailsWithStrings()
    {
        // String shorter than minimum
        $validator = new Validator(['name' => 'Jo'], ['name' => 'min:3']);
        $this->assertTrue($validator->fails());

        // Empty string
        $validator = new Validator(['name' => ''], ['name' => 'min:1']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test that the min rule fails with invalid numbers
     */
    public function testMinRuleFailsWithNumbers()
    {
        // Number below minimum
        $validator = new Validator(['age' => 17], ['age' => 'min:18']);
        $this->assertTrue($validator->fails());

        // String number below minimum
        $validator = new Validator(['age' => '17'], ['age' => 'min:18']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test that the min rule fails with invalid arrays
     */
    public function testMinRuleFailsWithArrays()
    {
        // Array with fewer items than minimum
        $validator = new Validator(['items' => [1, 2]], ['items' => 'array|min:3']);
        $this->assertTrue($validator->fails());

        // Empty array
        $validator = new Validator(['items' => []], ['items' => 'array|min:1']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testMinErrorMessage()
    {
        // For strings
        $validator = new Validator(['name' => 'Jo'], ['name' => 'min:3']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('name', $errors);
        $this->assertStringContainsString('name must be at least 3', $errors['name'][0]);

        // For numbers
        $validator = new Validator(['age' => 17], ['age' => 'min:18']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('age', $errors);
        $this->assertStringContainsString('age must be at least 18', $errors['age'][0]);
    }
}
