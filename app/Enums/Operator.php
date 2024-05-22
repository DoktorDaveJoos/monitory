<?php

namespace App\Enums;

enum Operator: string
{
    case EQUALS = 'equals';
    case NOT_EQUALS = 'not_equals';
    case GREATER_THAN = 'greater_than';
    case LESS_THAN = 'less_than';
    case GREATER_THAN_OR_EQUALS = 'greater_than_or_equals';
    case LESS_THAN_OR_EQUALS = 'less_than_or_equals';
    case CONTAINS = 'contains';
    case NOT_CONTAINS = 'not_contains';
    case REGEX = 'regex';
    case NOT_REGEX = 'not_regex';
    case STARTS_WITH = 'starts_with';
    case ENDS_WITH = 'ends_with';
    case IS_EMPTY = 'is_empty';
    case IS_NOT_EMPTY = 'is_not_empty';
    case IS_NULL = 'is_null';
    case IS_NOT_NULL = 'is_not_null';
    case IN = 'in';
    case NOT_IN = 'not_in';
    case BETWEEN = 'between';
    case NOT_BETWEEN = 'not_between';
    case MATCHES = 'matches';
    case NOT_MATCHES = 'not_matches';
    case IS_TRUE = 'is_true';
    case IS_FALSE = 'is_false';
    case IS_JSON = 'is_json';
    case IS_NOT_JSON = 'is_not_json';
    case IS_ARRAY = 'is_array';
    case IS_NOT_ARRAY = 'is_not_array';
    case IS_OBJECT = 'is_object';
    case IS_NOT_OBJECT = 'is_not_object';
    case IS_STRING = 'is_string';
    case IS_NOT_STRING = 'is_not_string';
    case IS_NUMERIC = 'is_numeric';
    case IS_NOT_NUMERIC = 'is_not_numeric';
    case IS_INTEGER = 'is_integer';
    case IS_NOT_INTEGER = 'is_not_integer';
    case IS_FLOAT = 'is_float';
    case IS_NOT_FLOAT = 'is_not_float';
    case IS_BOOLEAN = 'is_boolean';
    case IS_NOT_BOOLEAN = 'is_not_boolean';
    case IS_SCALAR = 'is_scalar';
    case IS_NOT_SCALAR = 'is_not_scalar';
    case IS_CALLABLE = 'is_callable';
    case IS_NOT_CALLABLE = 'is_not_callable';
    case IS_ITERABLE = 'is_iterable';
    case IS_NOT_ITERABLE = 'is_not_iterable';
    case IS_COUNTABLE = 'is_countable';
    case IS_NOT_COUNTABLE = 'is_not_countable';
    case IS_RESOURCE = 'is_resource';
    case IS_NOT_RESOURCE = 'is_not_resource';

    public function getLabel(): string
    {
        return match ($this) {
            self::EQUALS => 'Equals',
            self::NOT_EQUALS => 'Not Equals',
            self::GREATER_THAN => 'Greater Than',
            self::LESS_THAN => 'Less Than',
            self::GREATER_THAN_OR_EQUALS => 'Greater Than or Equals',
            self::LESS_THAN_OR_EQUALS => 'Less Than or Equals',
            self::CONTAINS => 'Contains',
            self::NOT_CONTAINS => 'Not Contains',
            self::REGEX => 'Regex',
            self::NOT_REGEX => 'Not Regex',
            self::STARTS_WITH => 'Starts With',
            self::ENDS_WITH => 'Ends With',
            self::IS_EMPTY => 'Is Empty',
            self::IS_NOT_EMPTY => 'Is Not Empty',
            self::IS_NULL => 'Is Null',
            self::IS_NOT_NULL => 'Is Not Null',
            self::IN => 'In',
            self::NOT_IN => 'Not In',
            self::BETWEEN => 'Between',
            self::NOT_BETWEEN => 'Not Between',
            self::MATCHES => 'Matches',
            self::NOT_MATCHES => 'Not Matches',
            self::IS_TRUE => 'Is True',
            self::IS_FALSE => 'Is False',
            self::IS_JSON => 'Is JSON',
            self::IS_NOT_JSON => 'Is Not JSON',
            self::IS_ARRAY => 'Is Array',
            self::IS_NOT_ARRAY => 'Is Not Array',
            self::IS_OBJECT => 'Is Object',
            self::IS_NOT_OBJECT => 'Is Not Object',
            self::IS_STRING => 'Is String',
            self::IS_NOT_STRING => 'Is Not String',
            self::IS_NUMERIC => 'Is Numeric',
            self::IS_NOT_NUMERIC => 'Is Not Numeric',
            self::IS_INTEGER => 'Is Integer',
            self::IS_NOT_INTEGER => 'Is Not Integer',
            self::IS_FLOAT => 'Is Float',
            self::IS_NOT_FLOAT => 'Is Not Float',
            self::IS_BOOLEAN => 'Is Boolean',
            self::IS_NOT_BOOLEAN => 'Is Not Boolean',
            self::IS_SCALAR => 'Is Scalar',
            self::IS_NOT_SCALAR => 'Is Not Scalar',
            self::IS_CALLABLE => 'Is Callable',
            self::IS_NOT_CALLABLE => 'Is Not Callable',
            self::IS_ITERABLE => 'Is Iterable',
            self::IS_NOT_ITERABLE => 'Is Not Iterable',
            self::IS_COUNTABLE => 'Is Countable',
            self::IS_NOT_COUNTABLE => 'Is Not Countable',
            self::IS_RESOURCE => 'Is Resource',
            self::IS_NOT_RESOURCE => 'Is Not Resource',
        };
    }
}
