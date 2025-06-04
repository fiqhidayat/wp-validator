<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Fiqhidayat\WPValidator\Validator;

class NestedValidationTest extends TestCase
{
    /**
     * Test that nested attributes are correctly validated
     */
    public function testNestedAttributeValidation()
    {
        // Test a simple nested structure
        $validator = new Validator(
            [
                'discount' => ['regular' => 10, 'special' => 15],
                'customer' => ['address' => ['city' => 'New York', 'zip' => '10001']]
            ],
            [
                'discount.regular' => 'required|numeric',
                'discount.special' => 'required|numeric',
                'customer.address.city' => 'required|string',
                'customer.address.zip' => 'required|digits:5'
            ]
        );

        $this->assertTrue($validator->passes());

        // Check if validated data preserves the structure
        $validated = $validator->validated();
        $this->assertEquals(10, $validated['discount']['regular']);
        $this->assertEquals(15, $validated['discount']['special']);
        $this->assertEquals('New York', $validated['customer']['address']['city']);
        $this->assertEquals('10001', $validated['customer']['address']['zip']);
    }

    /**
     * Test that validation fails correctly for nested attributes
     */
    public function testNestedAttributeValidationFailure()
    {
        // Test with invalid nested data
        $validator = new Validator(
            [
                'discount' => ['regular' => 'not-a-number', 'special' => 15],
                'customer' => ['address' => ['city' => '', 'zip' => 'ABC']]
            ],
            [
                'discount.regular' => 'required|numeric',
                'discount.special' => 'required|numeric',
                'customer.address.city' => 'required|string',
                'customer.address.zip' => 'required|digits:5'
            ]
        );

        $this->assertTrue($validator->fails());
        $errors = $validator->errors();

        $this->assertArrayHasKey('discount.regular', $errors);
        $this->assertArrayHasKey('customer.address.city', $errors);
        $this->assertArrayHasKey('customer.address.zip', $errors);
    }

    /**
     * Test nullable nested attributes
     */
    public function testNullableNestedAttributes()
    {
        $validator = new Validator(
            [
                'user' => [
                    'profile' => null
                ]
            ],
            [
                'user.profile' => 'nullable|array',
                'user.profile.bio' => 'nullable|string'
            ]
        );

        $this->assertTrue($validator->passes());
    }

    /**
     * Test comparison between nested attributes
     */
    public function testNestedAttributeComparison()
    {
        $validator = new Validator(
            [
                'product' => [
                    'price' => [
                        'sale' => 50,
                        'regular' => 100
                    ]
                ]
            ],
            [
                'product.price.sale' => 'required|numeric|lt:product.price.regular',
                'product.price.regular' => 'required|numeric'
            ]
        );

        $this->assertTrue($validator->passes());
    }
}

// Run the tests if file is executed directly
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $test = new NestedValidationTest();
    $test->testNestedAttributeValidation();
    $test->testNestedAttributeValidationFailure();
    $test->testNullableNestedAttributes();
    $test->testNestedAttributeComparison();
    echo "All tests passed!\n";
}
