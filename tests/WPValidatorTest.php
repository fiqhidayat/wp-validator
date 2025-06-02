<?php

/**
 * Unit tests for WP Validator.
 * 
 * This file contains basic unit tests for the validation library.
 * These tests are meant to be run with PHPUnit.
 */

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class WPValidatorTest extends TestCase
{
    public function testRequiredRule()
    {
        $validator = new Validator(['name' => ''], ['name' => 'required']);
        $this->assertTrue($validator->fails());

        $validator = new Validator(['name' => 'John'], ['name' => 'required']);
        $this->assertTrue($validator->passes());
    }

    public function testEmailRule()
    {
        $validator = new Validator(['email' => 'not-an-email'], ['email' => 'email']);
        $this->assertTrue($validator->fails());

        $validator = new Validator(['email' => 'valid@example.com'], ['email' => 'email']);
        $this->assertTrue($validator->passes());
    }

    public function testMinRule()
    {
        // String length
        $validator = new Validator(['name' => 'Jo'], ['name' => 'min:3']);
        $this->assertTrue($validator->fails());

        $validator = new Validator(['name' => 'John'], ['name' => 'min:3']);
        $this->assertTrue($validator->passes());

        // Numeric value
        $validator = new Validator(['age' => 17], ['age' => 'min:18']);
        $this->assertTrue($validator->fails());

        $validator = new Validator(['age' => 18], ['age' => 'min:18']);
        $this->assertTrue($validator->passes());
    }

    public function testMaxRule()
    {
        // String length
        $validator = new Validator(['name' => 'Alexander'], ['name' => 'max:5']);
        $this->assertTrue($validator->fails());

        $validator = new Validator(['name' => 'Alex'], ['name' => 'max:5']);
        $this->assertTrue($validator->passes());

        // Numeric value
        $validator = new Validator(['age' => 26], ['age' => 'max:25']);
        $this->assertTrue($validator->fails());

        $validator = new Validator(['age' => 25], ['age' => 'max:25']);
        $this->assertTrue($validator->passes());
    }

    public function testNumericRule()
    {
        $validator = new Validator(['age' => 'not-a-number'], ['age' => 'numeric']);
        $this->assertTrue($validator->fails());

        $validator = new Validator(['age' => '25'], ['age' => 'numeric']);
        $this->assertTrue($validator->passes());

        $validator = new Validator(['age' => 25], ['age' => 'numeric']);
        $this->assertTrue($validator->passes());

        $validator = new Validator(['amount' => '25.5'], ['amount' => 'numeric']);
        $this->assertTrue($validator->passes());
    }

    public function testMultipleRules()
    {
        $data = [
            'name' => 'John',
            'email' => 'john@example.com',
            'age' => 25,
        ];

        $rules = [
            'name' => 'required|min:3|max:20',
            'email' => 'required|email',
            'age' => 'required|numeric|min:18|max:100',
        ];

        $validator = new Validator($data, $rules);
        $this->assertTrue($validator->passes());

        // Test with invalid data
        $data['email'] = 'not-an-email';
        $data['age'] = 15;

        $validator = new Validator($data, $rules);
        $this->assertTrue($validator->fails());
        $this->assertCount(2, $validator->errors()); // should have errors for email and age
    }
}
