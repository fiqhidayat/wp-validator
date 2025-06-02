<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class UrlRuleTest extends TestCase
{
    /**
     * Test that the url rule passes with valid URLs
     */
    public function testUrlRulePasses()
    {
        // HTTP URL
        $validator = new Validator(['url' => 'http://example.com'], ['url' => 'url']);
        $this->assertTrue($validator->passes());

        // HTTPS URL
        $validator = new Validator(['url' => 'https://example.com'], ['url' => 'url']);
        $this->assertTrue($validator->passes());

        // URL with path
        $validator = new Validator(['url' => 'https://example.com/path/to/page'], ['url' => 'url']);
        $this->assertTrue($validator->passes());

        // URL with query string
        $validator = new Validator(['url' => 'https://example.com?param=value'], ['url' => 'url']);
        $this->assertTrue($validator->passes());

        // URL with fragment
        $validator = new Validator(['url' => 'https://example.com#section'], ['url' => 'url']);
        $this->assertTrue($validator->passes());

        // URL with subdomain
        $validator = new Validator(['url' => 'https://sub.example.com'], ['url' => 'url']);
        $this->assertTrue($validator->passes());

        // URL with port
        $validator = new Validator(['url' => 'https://example.com:8080'], ['url' => 'url']);
        $this->assertTrue($validator->passes());

        // URL with credentials
        $validator = new Validator(['url' => 'https://user:pass@example.com'], ['url' => 'url']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['url' => null], ['url' => 'nullable|url']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the url rule fails with invalid URLs
     */
    public function testUrlRuleFails()
    {
        // Missing protocol
        $validator = new Validator(['url' => 'example.com'], ['url' => 'url']);
        $this->assertTrue($validator->fails());

        // Invalid protocol
        $validator = new Validator(['url' => 'invalid://example.com'], ['url' => 'url']);
        $this->assertTrue($validator->fails());

        // Invalid characters
        $validator = new Validator(['url' => 'https://example.com/path with spaces'], ['url' => 'url']);
        $this->assertTrue($validator->fails());

        // Plain text
        $validator = new Validator(['url' => 'not a url'], ['url' => 'url']);
        $this->assertTrue($validator->fails());

        // Empty string
        $validator = new Validator(['url' => ''], ['url' => 'url']);
        $this->assertTrue($validator->fails());

        // Number
        $validator = new Validator(['url' => 123], ['url' => 'url']);
        $this->assertTrue($validator->fails());

        // Array
        $validator = new Validator(['url' => ['http://example.com']], ['url' => 'url']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error message format
     */
    public function testUrlErrorMessage()
    {
        $validator = new Validator(['url' => 'not a url'], ['url' => 'url']);
        $validator->validate();
        $errors = $validator->errors();

        $this->assertArrayHasKey('url', $errors);
        $this->assertStringContainsString('must be a valid URL', $errors['url'][0]);
    }
}
