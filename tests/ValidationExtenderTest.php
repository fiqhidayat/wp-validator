<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\ValidationExtender;
use Fiqhidayat\WPValidator\Rule;
use Fiqhidayat\WPValidator\Validator;

/**
 * Mock custom rule for testing the ValidationExtender
 */
class CustomRule implements Rule
{
    /**
     * Check if the value is exactly 'valid_value'
     *
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @param mixed $validator
     * @return bool
     */
    public function passes($attribute, $value, array $parameters, $validator)
    {
        return $value === 'valid_value';
    }

    /**
     * Get the validation error message.
     *
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @return string
     */
    public function message($attribute, $value, array $parameters)
    {
        return "The {$attribute} must be 'valid_value'.";
    }
}

class ValidationExtenderTest extends TestCase
{
    /**
     * Store original rules
     *
     * @var array
     */
    protected array $originalRules;

    /**
     * Reset the RuleFactory rules before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Save original rules
        $this->originalRules = \Fiqhidayat\WPValidator\RuleFactory::$rules;
    }

    /**
     * Restore original rules after each test
     */
    protected function tearDown(): void
    {
        // Restore original rules
        \Fiqhidayat\WPValidator\RuleFactory::$rules = $this->originalRules;
        parent::tearDown();
    }

    /**
     * Test that ValidationExtender throws an exception with invalid rule name
     */
    public function testExtendThrowsExceptionWithInvalidRuleName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Rule name must be a non-empty string.');

        ValidationExtender::extend('', new CustomRule());
    }

    /**
     * Test that ValidationExtender throws an exception with non-string rule name
     */
    public function testExtendThrowsExceptionWithNonStringRuleName()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Rule name must be a non-empty string.');

        ValidationExtender::extend(123, new CustomRule());
    }

    /**
     * Test extending with a Rule instance
     */
    public function testExtendWithRuleInstance()
    {
        // Extend with a CustomRule instance
        ValidationExtender::extend('custom', new CustomRule());

        // Verify the rule was added to RuleFactory
        $this->assertArrayHasKey('custom', \Fiqhidayat\WPValidator\RuleFactory::$rules);
        $this->assertEquals(CustomRule::class, \Fiqhidayat\WPValidator\RuleFactory::$rules['custom']);

        // Create a validator and test that the rule works
        $validator = new Validator(['field' => 'valid_value'], ['field' => 'custom']);
        $this->assertTrue($validator->passes());

        $validator = new Validator(['field' => 'invalid_value'], ['field' => 'custom']);
        $this->assertTrue($validator->fails());
        $this->assertEquals("The field must be 'valid_value'.", $validator->errors()['field'][0]);
    }

    /**
     * Test extending with a Rule class name
     */
    public function testExtendWithRuleClassName()
    {
        // Extend with a class name
        ValidationExtender::extend('custom_class', CustomRule::class);

        // Verify the rule was added to RuleFactory
        $this->assertArrayHasKey('custom_class', \Fiqhidayat\WPValidator\RuleFactory::$rules);
        $this->assertEquals(CustomRule::class, \Fiqhidayat\WPValidator\RuleFactory::$rules['custom_class']);

        // Create a validator and test that the rule works
        $validator = new Validator(['field' => 'valid_value'], ['field' => 'custom_class']);
        $this->assertTrue($validator->passes());

        $validator = new Validator(['field' => 'invalid_value'], ['field' => 'custom_class']);
        $this->assertTrue($validator->fails());
        $this->assertEquals("The field must be 'valid_value'.", $validator->errors()['field'][0]);
    }

    /**
     * Test extending with a class that doesn't implement Rule interface
     */
    public function testExtendThrowsExceptionWithInvalidClass()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Custom rule class must implement Fiqhidayat\\WPValidator\\Rules\\Rule interface.");

        // Try to extend with a class that doesn't implement Rule
        ValidationExtender::extend('invalid_class', \stdClass::class);
    }

    /**
     * Test extending with a callable (currently not supported)
     */
    public function testExtendThrowsExceptionWithCallable()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Callable rules are not supported yet. Please provide a class that implements Rule interface.");

        // Try to extend with a closure
        ValidationExtender::extend('callable_rule', function ($attribute, $value) {
            return $value === 'valid_value';
        });
    }

    /**
     * Test extending with an invalid implementation
     */
    public function testExtendThrowsExceptionWithInvalidImplementation()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Custom rule implementation must be a class name or an instance that implements Rule interface.");

        // Try to extend with an invalid implementation (an integer)
        ValidationExtender::extend('invalid_impl', 123);
    }
}
