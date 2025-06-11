<?php

namespace Fiqhidayat\WPValidator\Rules;

class ExistsRule extends AbstractRule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @param mixed $validator
     * @return bool
     */
    public function passes($attribute, $value, array $parameters, $validator)
    {
        if (empty($parameters) || count($parameters) < 1) {
            return false;
        }

        // The first parameter is the table name
        $table = $parameters[0];

        // The second parameter is the column name (optional, defaults to attribute name)
        $column = isset($parameters[1]) ? $parameters[1] : $attribute;

        // Additional where conditions (optional)
        $whereColumn = isset($parameters[2]) ? $parameters[2] : null;
        $whereValue = isset($parameters[3]) ? $parameters[3] : null;

        return $this->checkExists($table, $column, $value, $whereColumn, $whereValue);
    }

    /**
     * Check if the value exists in the database.
     *
     * @param string $table
     * @param string $column
     * @param mixed $value
     * @param string|null $whereColumn
     * @param mixed $whereValue
     * @return bool
     */
    protected function checkExists($table, $column, $value, $whereColumn = null, $whereValue = null)
    {
        global $wpdb;

        if (!$wpdb) {
            return false;
        }

        // Sanitize table and column names
        $table = $this->sanitizeIdentifier($table);
        $column = $this->sanitizeIdentifier($column);

        // Build the base query
        $sql = $wpdb->prepare(
            "SELECT COUNT(*) FROM `{$wpdb->prefix}{$table}` WHERE `{$column}` = %s",
            $value
        );

        // Add additional where condition if specified
        if ($whereColumn && $whereValue !== null) {
            $whereColumn = $this->sanitizeIdentifier($whereColumn);
            $sql = $wpdb->prepare(
                "SELECT COUNT(*) FROM `{$wpdb->prefix}{$table}` WHERE `{$column}` = %s AND `{$whereColumn}` = %s",
                $value,
                $whereValue
            );
        }

        $count = $wpdb->get_var($sql);

        return $count > 0;
    }

    /**
     * Sanitize database identifiers (table/column names).
     *
     * @param string $identifier
     * @return string
     */
    protected function sanitizeIdentifier($identifier)
    {
        // Basic sanitization: only allow alphanumeric characters and underscores
        return preg_replace('/[^a-zA-Z0-9_]/', '', $identifier);
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
        $table = isset($parameters[0]) ? $parameters[0] : 'table';
        $column = isset($parameters[1]) ? $parameters[1] : $attribute;

        return "The selected {$attribute} does not exist in {$table}.{$column}.";
    }

    /**
     * Get the default error message for the rule.
     *
     * @return string
     */
    protected function getMessage()
    {
        return 'The selected :attribute does not exist.';
    }
}
