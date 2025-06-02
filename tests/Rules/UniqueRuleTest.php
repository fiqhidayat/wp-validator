<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;
use Brain\Monkey\Functions;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class UniqueRuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Set up mock for WordPress global functions and variables 
        if (!defined('ABSPATH')) {
            define('ABSPATH', '/var/www/html/');
        }

        // Mock the global $wpdb object
        global $wpdb;
        $wpdb = (object) [
            'prefix' => 'wp_',
            'prepare' => function ($query, ...$args) {
                return str_replace('%s', "'$args[0]'", $query);
            },
            'get_var' => function ($query) {
                // Mock database response based on query
                if (strpos($query, "'unique-value'") !== false) {
                    return '0'; // No matches, value is unique
                } else if (strpos($query, "'existing-value'") !== false) {
                    return '1'; // Value exists
                } else if (strpos($query, "'ignore-test'") !== false && strpos($query, "id != '123'") !== false) {
                    return '0'; // Value exists but is being ignored
                }
                return '0'; // Default to unique
            }
        ];
    }

    /**
     * Test that the unique rule passes with unique values
     */
    public function testUniqueRulePasses()
    {
        // Basic unique check
        $validator = new Validator(['username' => 'unique-value'], ['username' => 'unique:users']);
        $this->assertTrue($validator->passes());

        // Unique check with specified column
        $validator = new Validator(['email' => 'unique-value'], ['email' => 'unique:users,user_email']);
        $this->assertTrue($validator->passes());

        // Unique check with ignore ID
        $validator = new Validator(['username' => 'ignore-test'], ['username' => 'unique:users,username,123']);
        $this->assertTrue($validator->passes());

        // Test with nullable and null (should pass if nullable)
        $validator = new Validator(['username' => null], ['username' => 'nullable|unique:users']);
        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the unique rule fails with non-unique values
     */
    public function testUniqueRuleFails()
    {
        // Value already exists
        $validator = new Validator(['username' => 'existing-value'], ['username' => 'unique:users']);
        $this->assertTrue($validator->fails());

        // Value exists with specified column
        $validator = new Validator(['email' => 'existing-value'], ['email' => 'unique:users,user_email']);
        $this->assertTrue($validator->fails());

        // Missing table parameter
        $validator = new Validator(['username' => 'unique-value'], ['username' => 'unique:']);
        $this->assertTrue($validator->fails());
    }

    /**
     * Test error messages for unique rule
     */
    public function testUniqueErrorMessage()
    {
        $validator = new Validator(['username' => 'existing-value'], ['username' => 'unique:users']);
        $validator->fails();

        $errors = $validator->errors();
        $this->assertArrayHasKey('username', $errors);
        $this->assertEquals('The username has already been taken in the users table.', $errors['username'][0]);
    }
}
