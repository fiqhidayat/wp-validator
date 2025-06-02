<?php

namespace Fiqhidayat\WPValidator\Rules;

class UniqueRule extends AbstractRule
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
        global $wpdb;

        if (empty($parameters) || !isset($parameters[0])) {
            return false;
        }

        $table = $parameters[0];
        $column = isset($parameters[1]) ? $parameters[1] : $attribute;

        // Optional parameters for ignoring a specific ID
        $ignoreId = null;
        $ignoreColumn = 'id';

        if (isset($parameters[2])) {
            $ignoreId = $parameters[2];
        }

        if (isset($parameters[3])) {
            $ignoreColumn = $parameters[3];
        }

        // Build the query
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}{$table} WHERE {$column} = %s",
            $value
        );

        // Add the ignore clause if necessary
        if ($ignoreId !== null) {
            $query .= $wpdb->prepare(" AND {$ignoreColumn} != %s", $ignoreId);
        }

        $count = $wpdb->get_var($query);

        return (int) $count === 0;
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
        $table = isset($parameters[0]) ? $parameters[0] : '';

        return "The {$attribute} has already been taken in the {$table} table.";
    }
}
