<?php

namespace Fiqhidayat\WPValidator;

use Fiqhidayat\WPValidator\Rule;

class ValidationExtender
{
    /**
     * Add a custom validation rule.
     *
     * @param string $ruleName
     * @param mixed $ruleImplementation Either a callable or a class that implements Rule
     * @return void
     */
    public static function extend($ruleName, $ruleImplementation)
    {
        if (!is_string($ruleName) || empty($ruleName)) {
            throw new \InvalidArgumentException('Rule name must be a non-empty string.');
        }

        // If the implementation is a Rule object
        if ($ruleImplementation instanceof Rule) {
            RuleFactory::$rules[$ruleName] = get_class($ruleImplementation);
            return;
        }

        // If the implementation is a class name
        if (is_string($ruleImplementation) && class_exists($ruleImplementation)) {
            $implements = class_implements($ruleImplementation);

            if (!isset($implements[Rule::class])) {
                throw new \InvalidArgumentException(
                    "Custom rule class must implement Fiqhidayat\\WPValidator\\Rules\\Rule interface."
                );
            }

            RuleFactory::$rules[$ruleName] = $ruleImplementation;
            return;
        }

        // If the implementation is a closure or callable
        if (is_callable($ruleImplementation)) {
            // We could create a dynamic rule class here, but we'll implement that in a future version.
            throw new \InvalidArgumentException(
                "Callable rules are not supported yet. Please provide a class that implements Rule interface."
            );
        }

        throw new \InvalidArgumentException(
            "Custom rule implementation must be a class name or an instance that implements Rule interface."
        );
    }
}
