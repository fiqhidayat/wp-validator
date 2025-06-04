<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Fiqhidayat\WPValidator\Validator;

// Example data with nested structure
$data = [
    'product' => [
        'name' => 'Smartphone',
        'price' => [
            'regular' => 899.99,
            'sale' => 799.99
        ],
        'stock' => 100
    ],
    'customer' => [
        'name' => 'John Doe',
        'address' => [
            'street' => '123 Main St',
            'city' => 'New York',
            'zipcode' => '10001',
            'country' => 'USA'
        ],
        'contact' => [
            'email' => 'john@example.com',
            'phone' => '123-456-7890'
        ]
    ],
    'order' => [
        'quantity' => 2,
        'shipping_method' => 'express'
    ]
];

// Define validation rules using dot notation for nested fields
$rules = [
    'product.name' => 'required|string',
    'product.price.regular' => 'required|numeric',
    'product.price.sale' => 'required|numeric|lt:product.price.regular',
    'product.stock' => 'required|integer|gte:order.quantity',

    'customer.name' => 'required|string',
    'customer.address.city' => 'required|string',
    'customer.address.zipcode' => 'required|string',
    'customer.contact.email' => 'required|email',
    'customer.contact.phone' => 'required',

    'order.quantity' => 'required|integer|gt:0',
    'order.shipping_method' => 'required|in:standard,express,priority'
];

// Create validator
$validator = new Validator($data, $rules);

// Check if validation passes
if ($validator->passes()) {
    echo "✅ Validation passed!\n";
    echo "Validated data structure:\n";
    print_r($validator->validated());
} else {
    echo "❌ Validation failed!\n";
    echo "Errors:\n";
    print_r($validator->errors());
}

// Let's try with invalid data as well
echo "\n\nTesting with invalid data:\n";
$invalidData = $data;
$invalidData['product']['price']['sale'] = 999.99; // Higher than regular price
$invalidData['product']['stock'] = 1; // Not enough stock
$invalidData['customer']['contact']['email'] = 'invalid-email';

$invalidValidator = new Validator($invalidData, $rules);

if ($invalidValidator->fails()) {
    echo "✅ Validation correctly failed!\n";
    echo "Errors found:\n";
    print_r($invalidValidator->errors());
} else {
    echo "❌ Validation should have failed but passed\n";
}
