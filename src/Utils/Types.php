<?php
namespace Explt13\Nosmi\Utils;

final class Types
{
    static function is_primitive($value): bool
    {
        return is_scalar($value) || is_null($value);
    }

    static function array_is_assoc($value): bool
    {
        return count($value) === count(array_filter($value, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY));
    }
}