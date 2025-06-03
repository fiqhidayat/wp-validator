<?php

namespace Fiqhidayat\WPValidator;

use Fiqhidayat\WPValidator\Rules\{
    RequiredRule,
    EmailRule,
    MinRule,
    MaxRule,
    NumericRule,
    IntegerRule,
    StringRule,
    UrlRule,
    IpRule,
    AlphaRule,
    AlphaDashRule,
    AlphaNumRule,
    DateRule,
    InRule,
    NotInRule,
    RegexRule,
    BooleanRule,
    ConfirmedRule,
    DifferentRule,
    DigitsRule,
    DigitsBetweenRule,
    DimensionsRule,
    FileRule,
    FilledRule,
    GreaterThanRule,
    GreaterThanOrEqualRule,
    ImageRule,
    JsonRule,
    LessThanRule,
    LessThanOrEqualRule,
    MimesRule,
    MimeTypesRule,
    NullableRule,
    PresentRule,
    SameRule,
    SizeRule,
    TimezoneRule,
    UniqueRule,
    ArrayRule
};

class RuleFactory
{
    /**
     * Map of rule names to their classes.
     *
     * @var array
     */
    public static $rules = [
        'required' => RequiredRule::class,
        'email' => EmailRule::class,
        'min' => MinRule::class,
        'max' => MaxRule::class,
        'numeric' => NumericRule::class,
        'integer' => IntegerRule::class,
        'string' => StringRule::class,
        'url' => UrlRule::class,
        'ip' => IpRule::class,
        'alpha' => AlphaRule::class,
        'alpha_dash' => AlphaDashRule::class,
        'alpha_num' => AlphaNumRule::class,
        'date' => DateRule::class,
        'in' => InRule::class,
        'not_in' => NotInRule::class,
        'regex' => RegexRule::class,
        'boolean' => BooleanRule::class,
        'confirmed' => ConfirmedRule::class,
        'different' => DifferentRule::class,
        'digits' => DigitsRule::class,
        'digits_between' => DigitsBetweenRule::class,
        'dimensions' => DimensionsRule::class,
        'file' => FileRule::class,
        'filled' => FilledRule::class,
        'image' => ImageRule::class,
        'json' => JsonRule::class,
        'mimes' => MimesRule::class,
        'mimetypes' => MimeTypesRule::class,
        'nullable' => NullableRule::class,
        'present' => PresentRule::class,
        'same' => SameRule::class,
        'size' => SizeRule::class,
        'timezone' => TimezoneRule::class,
        'unique' => UniqueRule::class,
        'array' => ArrayRule::class,
        'gt' => GreaterThanRule::class,
        'gte' => GreaterThanOrEqualRule::class,
        'lt' => LessThanRule::class,
        'lte' => LessThanOrEqualRule::class,
    ];

    /**
     * Make a new Rule object.
     *
     * @param string $rule
     * @return \Fiqhidayat\WPValidator\Rules\Rule|null
     */
    public static function make($rule)
    {
        if (!isset(static::$rules[$rule])) {
            return null;
        }

        $className = static::$rules[$rule];

        if (!class_exists($className)) {
            return null;
        }

        return new $className;
    }
}
