<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class JsonRuleTest extends TestCase
{
    /**
     * Test that the json rule passes with valid JSON
     */
    public function testJsonRulePasses()
    {
        // Empty object
        $validator = new Validator(['data' => '{}'], ['data' => 'json']);
        $this->assertTrue($validator->passes());

        // Simple object
        $validator = new Validator(['data' => '{"name":"John","age":30}'], ['data' => 'json']);
        $this->assertTrue($validator->passes());

        // Array
        $validator = new Validator(['data' => '[1,2,3,4]'], ['data' => 'json']);
        $this->assertTrue($validator->passes());

        // Nested structures
        $validator = new Validator(['data' => '{"user":{"name":"John","roles":["admin","editor"]}}'], ['data' => 'json']);
        $this->assertTrue($validator->passes());

        // Boolean values
        $validator = new Validator(['data' => 'true'], ['data' => 'json']);
        $this->assertTrue($validator->passes());
        $validator = new Validator(['data' => 'false'], ['data' => 'json']);
        $this->assertTrue($validator->passes());

        // Null value
        $validator = new Validator(['data' => 'null'], ['data' => 'json']);
        $this->assertTrue($validator->passes());

        // Number
        $validator = new Validator(['data' => '123'], ['data' => 'json']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null value (should pass if nullable)
        $validator = new Validator(['data' => null], ['data' => 'nullable|json']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the json rule fails with invalid JSON
     */
    public function testJsonRuleFails()
    {
        // Malformed JSON (missing closing brace)
        $validator = new Validator(['data' => '{"name":"John"'], ['data' => 'json']);
        $this->assertTrue($validator->fails());

        // Invalid syntax (single quotes)
        $validator = new Validator(['data' => "{'name':'John'}"], ['data' => 'json']);
        $this->assertTrue($validator->fails());

        // Invalid syntax (unquoted property names)
        $validator = new Validator(['data' => '{name:"John"}'], ['data' => 'json']);
        $this->assertTrue($validator->fails());

        // Plain text
        $validator = new Validator(['data' => 'This is not JSON'], ['data' => 'json']);
        $this->assertTrue($validator->fails());

        // Empty string
        $validator = new Validator(['data' => ''], ['data' => 'json']);
        $this->assertTrue($validator->fails());

        // PHP array (not JSON string)
        $validator = new Validator(['data' => ['name' => 'John']], ['data' => 'json']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testJsonErrorMessage()
    {
        $validator = new Validator(['data' => 'This is not JSON'], ['data' => 'json']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('data', $errors);
        $this->assertStringContainsString('data must be a valid JSON string', $errors['data'][0]);
    }
}
