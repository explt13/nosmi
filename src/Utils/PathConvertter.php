<?php
namespace Explt13\Nosmi\Utils;

use Explt13\Nosmi\Exceptions\InvalidAssocArrayValueException;

class PathConvertter
{
    protected const PATH_PARAMETERS_TYPES = ['<string>' => '[a-zA-Z]+', '<int>' => '[0-9]+', '<slug>' => '[a-zA-Z0-9-]+'];

    public static function convertPathPatternToRegexp(string $path_pattern): string
    {
        $needs_convert = substr_count($path_pattern, ':');

        // If there is nothing to convert, return path
        if (empty($needs_convert)) return "^".$path_pattern."$";

        $path_pattern = self::normalizePathPattern($path_pattern);

        preg_match_all('#(?P<type><[a-z]+>):(?P<name>[a-z_]+)(?=/|$)#', $path_pattern, $path_parameters, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

        // If numbers of needed conversion are not equal to matched path_parameters;
        if ($needs_convert !== count($path_parameters)) {
            $actual_count = count($path_parameters);
            throw new \LogicException("Path: $path_pattern has the count of matched path parameters ($actual_count) less than needs to be converted ($needs_convert). Check path's named parameters syntax.");
        }
        $path_parameters = array_map(fn($wildcard) => array_filter($wildcard, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY), $path_parameters);

        foreach($path_parameters as $path_parameter) {
            $type = $path_parameter['type'];
            $name = $path_parameter['name'];
            self::validatePathParameterType($type);
            $path_pattern = self::makeRegexConversion($type, $name, $path_pattern);
        }
        return "^".$path_pattern."$";
    }

    private static function normalizePathPattern(string $path_pattern): string
    {
        return preg_replace_callback(
            '#(?<=/):([a-z_]+)#',
            function ($matches) {
                // If type is missing, inject default type
                return "<slug>:{$matches[1]}";
            },
            $path_pattern
        );
    }

    private static function makeRegexConversion(string $type, string $name, string $path_pattern): string
    {
        return preg_replace(
            "#$type:$name#",
            sprintf("(?P<%s>%s)", $name, self::PATH_PARAMETERS_TYPES[$type]),
            $path_pattern
        );
    }


    private static function validatePathParameterType(string $type): void
    {
        if (!in_array($type, array_keys(self::PATH_PARAMETERS_TYPES))) {
            throw new InvalidAssocArrayValueException('type', array_keys(self::PATH_PARAMETERS_TYPES), $type);
        }
    }
}