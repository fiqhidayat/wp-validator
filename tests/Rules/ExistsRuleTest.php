<?php

namespace Tests\Rules;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;
use Fiqhidayat\WPValidator\Rules\ExistsRule;

class ExistsRuleTest extends TestCase
{
    /**
     * Test that the exists rule passes when value exists in database
     */
    public function testExistsRulePasses()
    {
        // Mock wpdb for testing
        global $wpdb;

        // Create a mock wpdb object with the methods we need
        $wpdb = new class {
            public $prefix = 'wp_';

            public function prepare($query, ...$args)
            {
                return vsprintf(str_replace('%s', "'%s'", $query), $args);
            }

            public function get_var($query)
            {
                return 1; // Simulate record exists
            }
        };

        $validator = new Validator(
            ['email' => 'test@example.com'],
            ['email' => 'exists:users,email']
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * Test that the exists rule fails when value doesn't exist in database
     */
    public function testExistsRuleFails()
    {
        // Mock wpdb for testing
        global $wpdb;

        // Create a mock wpdb object
        $wpdb = new class {
            public $prefix = 'wp_';

            public function prepare($query, ...$args)
            {
                return vsprintf(str_replace('%s', "'%s'", $query), $args);
            }

            public function get_var($query)
            {
                return 0; // Simulate record doesn't exist
            }
        };

        $validator = new Validator(
            ['email' => 'nonexistent@example.com'],
            ['email' => 'exists:users,email']
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors());
    }

    /**
     * Test exists rule with additional where condition
     */
    public function testExistsRuleWithWhereCondition()
    {
        // Mock wpdb for testing
        global $wpdb;

        // Create a mock wpdb object
        $wpdb = new class {
            public $prefix = 'wp_';

            public function prepare($query, ...$args)
            {
                return vsprintf(str_replace('%s', "'%s'", $query), $args);
            }

            public function get_var($query)
            {
                return 1; // Simulate record exists with condition
            }
        };

        $validator = new Validator(
            ['email' => 'test@example.com'],
            ['email' => 'exists:users,email,status,active']
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * Test exists rule fails when parameters are missing
     */
    public function testExistsRuleFailsWithMissingParameters()
    {
        $validator = new Validator(
            ['email' => 'test@example.com'],
            ['email' => 'exists'] // Missing table parameter
        );

        $this->assertTrue($validator->fails());
    }

    /**
     * Test exists rule error message
     */
    public function testExistsErrorMessage()
    {
        // Mock wpdb for testing
        global $wpdb;

        // Create a mock wpdb object
        $wpdb = new class {
            public $prefix = 'wp_';

            public function prepare($query, ...$args)
            {
                return vsprintf(str_replace('%s', "'%s'", $query), $args);
            }

            public function get_var($query)
            {
                return 0; // Simulate record doesn't exist
            }
        };

        $validator = new Validator(
            ['email' => 'nonexistent@example.com'],
            ['email' => 'exists:users,email']
        );

        $this->assertTrue($validator->fails());
        $errors = $validator->errors();
        $this->assertStringContainsString('does not exist', $errors['email'][0]);
    }

    /**
     * Test exists rule when wpdb is not available
     */
    public function testExistsRuleWithoutWpdb()
    {
        // Clear global wpdb
        global $wpdb;
        $originalWpdb = $wpdb;
        $wpdb = null;

        $validator = new Validator(
            ['email' => 'test@example.com'],
            ['email' => 'exists:users,email']
        );

        $this->assertTrue($validator->fails());

        // Restore wpdb
        $wpdb = $originalWpdb;
    }
}
