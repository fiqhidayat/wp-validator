<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class ArrayRuleTest extends TestCase
{
    /**
     * Test that the array rule passes with valid arrays
     */
    public function testArrayRulePasses()
    {
        // Empty array
        $validator = new Validator(['list' => []], ['list' => 'array']);
        $this->assertTrue($validator->passes());

        // Simple array
        $validator = new Validator(['list' => ['a', 'b', 'c']], ['list' => 'array']);
        $this->assertTrue($validator->passes());

        // Associative array
        $validator = new Validator(['list' => ['name' => 'John', 'age' => 25]], ['list' => 'array']);
        $this->assertTrue($validator->passes());

        // Nested array
        $validator = new Validator(['list' => ['a' => ['b' => 'c']]], ['list' => 'array']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['list' => null], ['list' => 'nullable|array']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the array rule fails with non-array values
     */
    public function testArrayRuleFails()
    {
        // String
        $validator = new Validator(['list' => 'not an array'], ['list' => 'array']);
        $this->assertTrue($validator->fails());

        // Integer
        $validator = new Validator(['list' => 123], ['list' => 'array']);
        $this->assertTrue($validator->fails());

        // Boolean
        $validator = new Validator(['list' => true], ['list' => 'array']);
        $this->assertTrue($validator->fails());

        // Object (not an array)
        $validator = new Validator(['list' => (object)['a' => 1]], ['list' => 'array']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test wildcard validation in arrays
     */
    public function testArrayWildcardValidation()
    {
        // All strings - should pass
        $validator = new Validator(['skills' => ['PHP', 'JavaScript', 'HTML']], [
            'skills' => 'array',
            'skills.*' => 'string'
        ]);
        $this->assertTrue($validator->passes());

        // Contains non-string - should fail
        $validator = new Validator(['skills' => ['PHP', 123, 'HTML']], [
            'skills' => 'array',
            'skills.*' => 'string'
        ]);
        $this->assertTrue($validator->fails());

        // Nested arrays with wildcards
        $validator = new Validator([
            'works' => [
                ['company_name' => 'Acme', 'role' => 'Developer'],
                ['company_name' => 'XYZ', 'role' => 'Manager']
            ]
        ], [
            'works' => 'array',
            'works.*.company_name' => 'required|string',
            'works.*.role' => 'required|string'
        ]);
        $this->assertTrue($validator->passes());

        // Missing required field - should fail
        $validator = new Validator([
            'works' => [
                ['company_name' => 'Acme'],
                ['company_name' => 'XYZ', 'role' => 'Manager']
            ]
        ], [
            'works' => 'array',
            'works.*.company_name' => 'required|string',
            'works.*.role' => 'required|string'
        ]);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testArrayErrorMessage()
    {
        $validator = new Validator(['list' => 'not an array'], ['list' => 'array']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('list', $errors);
        $this->assertStringContainsString('list must be an array', $errors['list'][0]);
    }
}
