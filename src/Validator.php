<?php

namespace Fiqhidayat\WPValidator;

class Validator
{
    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data = [];

    /**
     * The validation rules.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * All of the error messages.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * The failed validation rules.
     *
     * @var array
     */
    protected $failedRules = [];

    /**
     * Indicates whether validation has failed.
     *
     * @var bool
     */
    protected $failed = false;

    /**
     * Create a new validator instance.
     *
     * @param array $data
     * @param array $rules
     * @return void
     */
    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;

        $this->validate();
    }

    /**
     * create a new validator instance from static context.
     *
     * @param array $data
     * @param array $rules
     * @return static
     */
    public static function make(array $data, array $rules)
    {
        return new static($data, $rules);
    }

    /**
     * Run the validator's rules against its data.
     *
     * @return array
     */
    public function validate()
    {
        $this->messages = [];
        $this->failedRules = [];
        $this->failed = false;

        foreach ($this->rules as $attribute => $rules) {
            $this->validateAttribute($attribute, $rules);
        }

        return $this->passes() ? $this->validated() : $this->messages;
    }

    /**
     * Validate a given attribute against a rule.
     *
     * @param string $attribute
     * @param string $rules
     * @return void
     */
    protected function validateAttribute($attribute, $rules)
    {
        // Check for wildcards in the attribute name
        if (strpos($attribute, '*') !== false) {
            $this->validateWildcardAttribute($attribute, $rules);
            return;
        }

        // Allow rules to be a string or an array
        if (is_array($rules)) {
            $ruleArray = $rules;
        } else {
            $ruleArray = explode('|', $rules);
        }

        // Ensure the attribute exists in data structure when using dot notation
        if (strpos($attribute, '.') !== false) {
            // Check if any parent path is nullable and null
            if ($this->shouldSkipDueToNullableParent($attribute)) {
                return;
            }

            $value = $this->getDataByPath($attribute);

            // Skip validation if the nested path doesn't exist
            if ($value === null && !$this->isNullable($attribute)) {
                // For required fields, we still need to validate
                $hasRequiredRule = false;
                foreach ($ruleArray as $rule) {
                    if ($rule === 'required' || strpos($rule, 'required:') === 0) {
                        $hasRequiredRule = true;
                        break;
                    }
                }

                if (!$hasRequiredRule) {
                    return;
                }
            }
        }

        foreach ($ruleArray as $rule) {
            $this->validateRule($attribute, $rule);
        }
    }

    /**
     * Validate a wildcard attribute against a rule.
     *
     * @param string $attribute
     * @param string $rules
     * @return void
     */
    protected function validateWildcardAttribute($attribute, $rules)
    {
        // Handle dot notation for nested arrays
        if (strpos($attribute, '.') !== false && strpos($attribute, '.*') !== false) {
            $segments = explode('.', $attribute);
            $wildcardPosition = array_search('*', $segments);

            if ($wildcardPosition !== false) {
                // Get the parent segment (before wildcard)
                $parentPath = implode('.', array_slice($segments, 0, $wildcardPosition));

                // Get the child segment (after wildcard)
                $childPath = $wildcardPosition < count($segments) - 1 ?
                    implode('.', array_slice($segments, $wildcardPosition + 1)) :
                    null;

                // Get parent data through dot notation
                $parentData = $this->getDataByPath($parentPath);

                // Skip validation if parent doesn't exist or is not array or is null and nullable
                if (!is_array($parentData)) {
                    $parentKey = explode('.', $parentPath)[0];
                    if (!isset($this->data[$parentKey]) || ($this->isNullable($parentKey) && $this->data[$parentKey] === null)) {
                        return;
                    }
                    return;
                }

                $ruleArray = explode('|', $rules);

                // Validate each item in the parent array
                foreach ($parentData as $index => $item) {
                    $currentPath = $parentPath . '.' . $index;

                    if ($childPath) {
                        // Handle deeper nesting (e.g., works.*.company_name)
                        if (is_array($item)) {
                            $childValue = $this->getNestedValue($item, $childPath);
                            $fullPath = $currentPath . '.' . $childPath;

                            foreach ($ruleArray as $rule) {
                                $this->validateNestedItem($fullPath, $rule, $childValue);
                            }
                        }
                    } else {
                        // Simple array item (e.g., skills.*)
                        foreach ($ruleArray as $rule) {
                            $this->validateNestedItem($currentPath, $rule, $item);
                        }
                    }
                }

                return;
            }
        }

        // Original wildcard logic for direct pattern matching
        $pattern = str_replace('*', '([^.]+)', $attribute);
        $pattern = '/^' . str_replace('.', '\.', $pattern) . '$/';

        foreach ($this->data as $key => $value) {
            if (preg_match($pattern, $key)) {
                $ruleArray = explode('|', $rules);
                foreach ($ruleArray as $rule) {
                    $this->validateRule($key, $rule);
                }
            }
        }
    }

    /**
     * Validate a single rule.
     *
     * @param string $attribute
     * @param string $rule
     * @return void
     */
    protected function validateRule($attribute, $rule)
    {
        $parameters = [];
        if (strpos($rule, ':') !== false) {
            list($rule, $paramStr) = explode(':', $rule, 2);
            $parameters = explode(',', $paramStr);
        }

        $value = $this->getDataByPath($attribute);

        // Skip validation if the field is nullable and the value is null
        if ($rule !== 'nullable' && $this->isNullable($attribute) && $value === null) {
            return;
        }

        $ruleObject = RuleFactory::make($rule);

        if (!$ruleObject || $ruleObject->passes($attribute, $value, $parameters, $this)) {
            return;
        }

        $this->failed = true;
        $this->failedRules[$attribute][$rule] = $parameters;

        if (!isset($this->messages[$attribute])) {
            $this->messages[$attribute] = [];
        }

        $this->messages[$attribute][] = $ruleObject->message($attribute, $value, $parameters);
    }

    /**
     * Determine if an attribute is nullable.
     *
     * @param string $attribute
     * @return bool
     */
    protected function isNullable($attribute)
    {
        // Direct match for the exact attribute
        if (isset($this->rules[$attribute])) {
            $rules = is_array($this->rules[$attribute]) ? implode('|', $this->rules[$attribute]) : $this->rules[$attribute];
            return strpos($rules, 'nullable') !== false;
        }

        // Check parent paths for nested attributes
        if (strpos($attribute, '.') !== false) {
            $segments = explode('.', $attribute);
            $currentPath = '';

            foreach ($segments as $segment) {
                $currentPath = $currentPath ? $currentPath . '.' . $segment : $segment;

                if (isset($this->rules[$currentPath])) {
                    $rules = is_array($this->rules[$currentPath]) ?
                        implode('|', $this->rules[$currentPath]) :
                        $this->rules[$currentPath];

                    if (strpos($rules, 'nullable') !== false) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Determine if the validation failed.
     *
     * @return bool
     */
    public function fails()
    {
        return $this->failed;
    }

    /**
     * Determine if the validation passed.
     *
     * @return bool
     */
    public function passes()
    {
        return !$this->failed;
    }

    /**
     * Get the validated data.
     *
     * @return array
     */
    public function validated()
    {
        $validated = [];

        foreach ($this->rules as $key => $value) {
            // Skip wildcard rules as they are handled separately
            if (strpos($key, '*') !== false) {
                continue;
            }

            // Handle dot notation for nested keys
            if (strpos($key, '.') !== false) {
                $fieldValue = $this->getDataByPath($key);
                if ($fieldValue !== null) {
                    $this->setNestedValue($validated, $key, $fieldValue);
                }
            } else if (isset($this->data[$key])) {
                $validated[$key] = $this->data[$key];
            }
        }

        return $validated;
    }

    /**
     * Get all error messages.
     *
     * @return array
     */
    public function errors()
    {
        return $this->messages;
    }

    /**
     * Get the attributes and values that were validated.
     *
     * @return array
     */
    public function attributes()
    {
        return $this->data;
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Validate a nested item against a rule.
     *
     * @param string $attribute
     * @param string $rule
     * @param mixed $value
     * @return void
     */
    protected function validateNestedItem($attribute, $rule, $value)
    {
        $parameters = [];
        if (strpos($rule, ':') !== false) {
            list($rule, $paramStr) = explode(':', $rule, 2);
            $parameters = explode(',', $paramStr);
        }

        // Skip validation if the field is nullable and the value is null
        if ($rule !== 'nullable' && $this->isNestedNullable($attribute) && $value === null) {
            return;
        }

        $ruleObject = RuleFactory::make($rule);

        if (!$ruleObject || $ruleObject->passes($attribute, $value, $parameters, $this)) {
            return;
        }

        $this->failed = true;
        $this->failedRules[$attribute][$rule] = $parameters;

        if (!isset($this->messages[$attribute])) {
            $this->messages[$attribute] = [];
        }

        $this->messages[$attribute][] = $ruleObject->message($attribute, $value, $parameters);
    }

    /**
     * Determine if a nested attribute is nullable.
     *
     * @param string $attribute
     * @return bool
     */
    protected function isNestedNullable($attribute)
    {
        // For nested attributes like skills.0, check if skills.* has nullable rule
        if (strpos($attribute, '.') !== false) {
            $segments = explode('.', $attribute);
            $wildcard = $segments[0] . '.*';

            if (isset($this->rules[$wildcard])) {
                return strpos($this->rules[$wildcard], 'nullable') !== false;
            }

            // For deeply nested attributes (works.0.company_name), check if works.*.company_name has nullable rule
            if (count($segments) > 2) {
                $wildcardPath = $segments[0] . '.*.' . implode('.', array_slice($segments, 2));
                if (isset($this->rules[$wildcardPath])) {
                    return strpos($this->rules[$wildcardPath], 'nullable') !== false;
                }
            }
        }

        return false;
    }

    /**
     * Get data by dot notation path
     *
     * @param string $path
     * @return mixed
     */
    protected function getDataByPath($path)
    {
        $segments = explode('.', $path);
        $data = $this->data;

        foreach ($segments as $segment) {
            if (!is_array($data) || !isset($data[$segment])) {
                return null;
            }
            $data = $data[$segment];
        }

        return $data;
    }

    /**
     * Get a nested value from an array using dot notation
     *
     * @param array $array
     * @param string $path
     * @return mixed
     */
    protected function getNestedValue($array, $path)
    {
        $segments = explode('.', $path);
        $value = $array;

        foreach ($segments as $segment) {
            if (!is_array($value) || !isset($value[$segment])) {
                return null;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Get the value of a field from the data being validated.
     *
     * @param string $field The field name (supports dot notation for nested arrays)
     * @return mixed The field value
     */
    public function getValue($field)
    {
        return $this->getDataByPath($field);
    }

    /**
     * Set a nested value in an array using dot notation
     *
     * @param array &$array The array to set the value in
     * @param string $path The path in dot notation
     * @param mixed $value The value to set
     * @return void
     */
    protected function setNestedValue(&$array, $path, $value)
    {
        $segments = explode('.', $path);
        $current = &$array;

        // Build the nested structure
        foreach ($segments as $i => $segment) {
            // If we're at the last segment, set the value
            if ($i === count($segments) - 1) {
                $current[$segment] = $value;
                break;
            }

            // If the segment doesn't exist or is not an array, create it
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                $current[$segment] = [];
            }

            // Move deeper into the array
            $current = &$current[$segment];
        }
    }

    /**
     * Check if validation should be skipped due to a nullable parent being null.
     *
     * @param string $attribute
     * @return bool
     */
    protected function shouldSkipDueToNullableParent($attribute)
    {
        if (strpos($attribute, '.') === false) {
            return false;
        }

        $segments = explode('.', $attribute);
        $currentPath = '';

        // Check each parent path
        foreach ($segments as $i => $segment) {
            $currentPath = $currentPath ? $currentPath . '.' . $segment : $segment;

            // Don't check the final segment (the attribute itself)
            if ($i === count($segments) - 1) {
                break;
            }

            // Check if this parent path is nullable and null
            if (isset($this->rules[$currentPath])) {
                $rules = is_array($this->rules[$currentPath]) ?
                    implode('|', $this->rules[$currentPath]) :
                    $this->rules[$currentPath];

                if (strpos($rules, 'nullable') !== false) {
                    $parentValue = $this->getDataByPath($currentPath);
                    if ($parentValue === null) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
