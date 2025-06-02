<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class SizeRuleTest extends TestCase
{
    /**
     * Test that the size rule passes with correct size values for different types
     */
    public function testSizeRulePasses()
    {
        // String with exact character count
        $validator = new Validator(['name' => 'John'], ['name' => 'size:4']);
        $this->assertTrue($validator->passes());

        // Numeric value exactly matching size
        $validator = new Validator(['age' => 25], ['age' => 'size:25']);
        $this->assertTrue($validator->passes());

        // Array with exact item count
        $validator = new Validator(['items' => [1, 2, 3]], ['items' => 'size:3']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['name' => null], ['name' => 'nullable|size:4']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the size rule fails with incorrect size values
     */
    public function testSizeRuleFails()
    {
        // String with wrong character count
        $validator = new Validator(['name' => 'John Doe'], ['name' => 'size:4']);
        $this->assertTrue($validator->fails());

        // String with wrong character count (too few)
        $validator = new Validator(['name' => 'Joe'], ['name' => 'size:4']);
        $this->assertTrue($validator->fails());

        // Numeric value not matching
        $validator = new Validator(['age' => 30], ['age' => 'size:25']);
        $this->assertTrue($validator->fails());

        // Array with wrong item count
        $validator = new Validator(['items' => [1, 2, 3, 4]], ['items' => 'size:3']);
        $this->assertTrue($validator->fails());

        // Missing parameter
        $validator = new Validator(['name' => 'John'], ['name' => 'size:']);
        $this->assertTrue($validator->fails());

        // Non-numeric parameter
        $validator = new Validator(['name' => 'John'], ['name' => 'size:abc']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error messages for size rule
     */
    public function testSizeErrorMessage()
    {
        // String validation error
        $validator = new Validator(['name' => 'John Doe'], ['name' => 'size:4']);
        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('name', $errors);
        $this->assertEquals('The name must be 4 characters.', $errors['name'][0]);

        // Numeric value error
        $validator = new Validator(['age' => 30], ['age' => 'size:25']);
        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('age', $errors);
        $this->assertEquals('The age must be 25.', $errors['age'][0]);

        // Array error
        $validator = new Validator(['items' => [1, 2, 3, 4]], ['items' => 'size:3']);
        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('items', $errors);
        $this->assertEquals('The items must contain 3 items.', $errors['items'][0]);
    }
}
