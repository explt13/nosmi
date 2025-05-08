<?php

namespace Explt13\Nosmi\Utils;

class Utils
{
    public static function getConstant(string $name): ?string
    {
        if (defined($name)) {
            return constant($name);
        }
        return null;
    }

    public static function getArrayValue(array $array, string $name)
    {
        if (isset($array[$name])) {
            return $array[$name];
        }
        return null;
    }

    public static function flattenArray(array $array)
    {
        $result = [];
        array_walk_recursive($array, function ($item) use (&$result) {
            $result[] = $item;
        });
        return $result;
    }
}