<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class NullableRuleTest extends TestCase
{
    /**
     * Test that the nullable rule allows null values
     */
    public function testNullableRulePasses()
    {
        // Null with string validation
        $validator = new Validator(['name' => null], ['name' => 'nullable|string']);
        $this->assertTrue($validator->passes());

        // Null with numeric validation
        $validator = new Validator(['age' => null], ['age' => 'nullable|numeric']);
        $this->assertTrue($validator->passes());

        // Empty string
        $validator = new Validator(['name' => ''], ['name' => 'nullable|min:3']);
        $this->assertTrue($validator->passes());

        // Missing field
        $validator = new Validator([], ['name' => 'nullable|string']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that other rules are still applied if value is not null
     */
    public function testNullableRuleWithOtherRules()
    {
        // Should pass (valid email)
        $validator = new Validator(['email' => 'test@example.com'], ['email' => 'nullable|email']);
        $this->assertTrue($validator->passes());

        // Should fail (invalid email)
        $validator = new Validator(['email' => 'not-valid-email'], ['email' => 'nullable|email']);
        $this->assertTrue($validator->fails());

        // Should pass (numeric)
        $validator = new Validator(['age' => 25], ['age' => 'nullable|numeric']);
        $this->assertTrue($validator->passes());

        // Should fail (not numeric)
        $validator = new Validator(['age' => 'twenty-five'], ['age' => 'nullable|numeric']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test nullable rule with arrays and nested validation
     */
    public function testNullableWithArrays()
    {
        // Array can be null
        $validator = new Validator(['skills' => null], [
            'skills' => 'nullable|array',
            'skills.*' => 'string'
        ]);
        $this->assertTrue($validator->passes());

        // Valid array items
        $validator = new Validator(['skills' => ['PHP', 'JavaScript']], [
            'skills' => 'nullable|array',
            'skills.*' => 'string'
        ]);
        $this->assertTrue($validator->passes());

        // Invalid array items (but array is not null)
        $validator = new Validator(['skills' => [1, 2, 3]], [
            'skills' => 'nullable|array',
            'skills.*' => 'string'
        ]);
        $this->assertTrue($validator->fails());
    }
}
