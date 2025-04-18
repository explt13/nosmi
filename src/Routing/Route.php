<?php

namespace Explt13\Nosmi\Routing;

use Explt13\Nosmi\Base\Controller;
use Explt13\Nosmi\Exceptions\InvalidAssocArrayValueException;

class Route
{
    private array $params = [];
    private string $controller;
    private string $regexp;
    private static array $routes = [];
    private static array $patterns_map = [];
    private const PATH_PARAMETERS_TYPES = ['<string>' => '[a-zA-Z]+', '<int>' => '[0-9]+', '<slug>' => '[a-zA-Z0-9-]+'];

    public function setRoute(string $path): void
    {
        if (empty(self::$routes)) {
            throw new \LogicException('No routes found, make sure you added them correctly.');
        }
        $this->resolvePath($path);
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getParams(): array
    {
        return $this->params;
    }
    
    public function getPathRegexp(): string
    {
        return $this->regexp;
    }

    public function getPathPattern(): string
    {
        return array_search($this->regexp, self::$patterns_map);
    }

    public static function add(string $path_pattern, string $controller): void
    {
        $regexp = self::convertPathPatternToRegexp($path_pattern);
        self::$patterns_map[$path_pattern] = $regexp;
        self::$routes[$regexp] = $controller;
    }

    public static function getPatternToRegexMap(): array
    {
        return self::$patterns_map;
    }

    public static function getPathPatterns(): array
    {
        return array_keys(self::$patterns_map);
    }

    public static function getPathRegexps(): array
    {
        return array_values(self::$patterns_map);
    }

    public static function getRoutes(): array
    {
        return self::$routes;
    }

    public static function getRegexpByPathPattern(string $path_pattern): string
    {
        return self::$patterns_map[$path_pattern];
    }

    public static function getControllerByPathPattern(string $path_pattern): string
    {
        $regexp = self::$patterns_map[$path_pattern];
        return self::$routes[$regexp];
    }

    public static function getControllerByRegexp(string $regexp): string
    {
        return self::$routes[$regexp];
    }

    public static function getControllerPatternPaths(string $controller): array
    {
        $regexps = self::getControllerRegexps($controller);
        $patterns = array_keys(array_filter(self::$patterns_map, function($regexp) use ($regexps) {
            return in_array($regexp, $regexps, true);
        }));
        return $patterns;
    }

    public static function getControllerRegexps(string $controller): array
    {
        return array_keys(self::$routes, $controller, true);
    }

    private static function convertPathPatternToRegexp(string $path_pattern): string
    {
        $needs_convert = substr_count($path_pattern, ':');

        // If there is nothing to convert, return path
        if (empty($needs_convert)) return $path_pattern;

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
        return $path_pattern;
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

    private function resolvePath(string $path): void
    {
        foreach (self::$routes as $regexp => $controller) {
            if (preg_match("#$regexp#", $path, $parameters)) {
                $this->regexp = $regexp;
                $this->controller = $controller;
                $parameters = array_filter($parameters, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
                $this->setPathParams($parameters);
            }
            throw new \Exception("Route `$path` is not found", 404);
        }
    }

    private function setPathParams(array $parameters): void
    {
        foreach ($parameters as $name => $value) {
            $this->params[$name] = $value;
        }
    }
}