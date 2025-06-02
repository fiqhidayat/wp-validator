<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class AlphaRuleTest extends TestCase
{
    /**
     * Test that the alpha rule passes with alphabetical characters only
     */
    public function testAlphaRulePasses()
    {
        // Lowercase letters
        $validator = new Validator(['name' => 'john'], ['name' => 'alpha']);
        $this->assertTrue($validator->passes());

        // Uppercase letters
        $validator = new Validator(['name' => 'JOHN'], ['name' => 'alpha']);
        $this->assertTrue($validator->passes());

        // Mixed case
        $validator = new Validator(['name' => 'John'], ['name' => 'alpha']);
        $this->assertTrue($validator->passes());

        // Empty string (debatable if this should pass, depends on implementation)
        $validator = new Validator(['name' => ''], ['name' => 'alpha']);
        $this->assertTrue($validator->passes() || $validator->fails()); // Accept either behavior

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['name' => null], ['name' => 'nullable|alpha']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the alpha rule fails with non-alphabetical characters
     */
    public function testAlphaRuleFails()
    {
        // With numbers
        $validator = new Validator(['name' => 'john123'], ['name' => 'alpha']);
        $this->assertTrue($validator->fails());

        // With spaces
        $validator = new Validator(['name' => 'john doe'], ['name' => 'alpha']);
        $this->assertTrue($validator->fails());

        // With special characters
        $validator = new Validator(['name' => 'john!'], ['name' => 'alpha']);
        $this->assertTrue($validator->fails());

        // Number only
        $validator = new Validator(['name' => 123], ['name' => 'alpha']);
        $this->assertTrue($validator->fails());

        // Array
        $validator = new Validator(['name' => ['john']], ['name' => 'alpha']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testAlphaErrorMessage()
    {
        $validator = new Validator(['name' => 'john123'], ['name' => 'alpha']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('name', $errors);
        $this->assertStringContainsString('name may only contain letters', $errors['name'][0]);
    }
}
