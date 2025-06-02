# WP Validator

A Laravel-style validation library for WordPress forms.

## Installation

```
composer require fiqhidayat/wp-validator
```

## Usage

```php
use Fiqhidayat\WPValidator\Validator;

// Create a validator instance
$validator = new Validator([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 25,
], [
    'name' => 'required|min:3',
    'email' => 'required|email',
    'age' => 'required|numeric|min:18',
]);

// Check if validation passes
if ($validator->passes()) {
    // Validation passed
    $validatedData = $validator->validated();
} else {
    // Validation failed
    $errors = $validator->errors();
}
```

## Array Validation Example

The library supports validating nested arrays using wildcards:

```php
// Complex data structure with nested arrays
$data = [
    'user' => [
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ],
    'skills' => ['PHP', 'JavaScript', 'HTML', 'CSS'],
    'works' => [
        [
            'company_name' => 'Acme Inc',
            'role' => 'Developer',
            'start_date' => '2020-01-01'
        ],
        [
            'company_name' => 'XYZ Corp',
            'role' => 'Senior Developer',
            'start_date' => '2022-05-01'
        ]
    ]
];

// Define validation rules
$rules = [
    'user' => 'required|array',
    'user.name' => 'required|string|min:3',
    'user.email' => 'required|email',
    'skills' => 'nullable|array',
    'skills.*' => 'string',
    'works' => 'nullable|array',
    'works.*.company_name' => 'required|string',
    'works.*.role' => 'required|string',
    'works.*.start_date' => 'nullable|date'
];

$validator = new Validator($data, $rules);

if ($validator->passes()) {
    // All data is valid
} else {
    // Get validation errors
    $errors = $validator->errors();
}
```

## WordPress Form Example

Here's an example of validating a contact form submission in WordPress:

```php
function process_contact_form() {
    // Check nonce for security
    check_ajax_referer('contact_form_nonce', 'security');
    
    // Get form data
    $data = [
        'name' => sanitize_text_field($_POST['name']),
        'email' => sanitize_email($_POST['email']),
        'message' => sanitize_textarea_field($_POST['message']),
    ];
    
    // Set validation rules
    $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'message' => 'required|min:10',
    ];
    
    // Create validator
    $validator = new Fiqhidayat\WPValidator\Validator($data, $rules);
    
    // Check if validation fails
    if ($validator->fails()) {
        wp_send_json_error([
            'success' => false,
            'errors' => $validator->errors(),
        ]);
    }
    
    // Process the form (save to database, send email, etc.)
    // ...
    
    wp_send_json_success([
        'success' => true, 
        'message' => 'Form submitted successfully!'
    ]);
}
add_action('wp_ajax_contact_form', 'process_contact_form');
add_action('wp_ajax_nopriv_contact_form', 'process_contact_form');
```

## Available Rules

This library supports most Laravel validation rules including:

- `required`: The field must be present and not empty
- `email`: The field must be a valid email address
- `min:value`: String/numeric minimum length or value
- `max:value`: String/numeric maximum length or value
- `numeric`: The field must be numeric
- `integer`: The field must be an integer
- `string`: The field must be a string
- `url`: The field must be a valid URL
- `ip`: The field must be a valid IP address
- `alpha`: The field must contain only alphabetic characters
- `alpha_dash`: The field may contain alpha-numeric characters, dashes, and underscores
- `alpha_num`: The field must contain only alpha-numeric characters
- `array`: The field must be a PHP array
- `date`: The field must be a valid date
- `in:foo,bar,...`: The field must be included in the given list of values
- `not_in:foo,bar,...`: The field must not be included in the given list of values
- `nullable`: The field can be null or empty string
- `regex:pattern`: The field must match the given regular expression
- `boolean`: The field must be a boolean value (true, false, 0, 1, '0', '1')
- `confirmed`: The field must have a matching field of {field}_confirmation
- `same:field`: The field must match the specified field
- `different:field`: The field must be different from the specified field
- `json`: The field must be a valid JSON string
- `timezone`: The field must be a valid timezone
- `file`: The field must be a successfully uploaded file
- `image`: The field must be an image file (jpeg, png, bmp, gif, svg, webp)
- `unique:table,column,except,idColumn`: The field must be unique in a database table

## Custom Implementation

You can extend this validator by creating your own rules. Here's how to create and register a custom validation rule:

```php
// Create a class that implements the Rule interface
class PalindromeRule implements Fiqhidayat\WPValidator\Rule 
{
    public function passes($attribute, $value, array $parameters, $validator) {
        $cleanStr = preg_replace('/[^a-z0-9]/i', '', strtolower($value));
        return $cleanStr === strrev($cleanStr);
    }
    
    public function message($attribute, $value, array $parameters) {
        return "The {$attribute} must be a palindrome.";
    }
}

// Register the custom rule
use Fiqhidayat\WPValidator\ValidationExtender;
ValidationExtender::extend('palindrome', PalindromeRule::class);

// Now use it in your validations
$validator = new Validator([
    'name' => 'Anna'
], [
    'name' => 'required|palindrome'
]);
```

## License

GPL-2.0-or-later
