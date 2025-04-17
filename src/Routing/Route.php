<?php

namespace Explt13\Nosmi\Routing;

use Explt13\Nosmi\Base\Controller;
use Explt13\Nosmi\Exceptions\InvalidAssocArrayValueException;

class Route
{
    private array $route = [];
    private static array $routes = [];
    private array $readOnlyProperties = [];
    private bool $canOverwrite = false;
    private const PATH_PARAMETERS_TYPES = ['<string>' => '[a-zA-Z]+', '<int>' => '[0-9]+', '<slug>' => '[a-zA-Z0-9-]+'];

    public function setRoute(string $path): void
    {
        $this->parseRoute($path);
        // $route = $this->prepareRoute($route);
        // foreach ($route as $key => $value) {
        //     $this->route[$key] = $value;
        //     $this->readOnlyProperties[] = $key;
        // }
    }

    public static function add(string $path, string $controller): void
    {
        $regexp = self::convertPathToPathPattern($path);
        self::$routes[$regexp] = $controller;
    }

    private static function convertPathToPathPattern(string $path): string
    {
        $needs_convert = substr_count($path, ':');

        // If there is nothing to convert, return path
        if (empty($needs_convert)) return $path;

        preg_match_all('#(?P<type><[a-z]+>|(?<=/))?:(?P<path_parameter>[a-z_]+)(?=/|$)#', $path, $wildcards, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

        // If numbers of needed conversion are not equal to matched wildcards;
        if ($needs_convert !== count($wildcards)) {
            $actual_count = count($wildcards);
            throw new \LogicException("Path: $path has the count of matched path parameters ($actual_count) less than needs to be converted ($needs_convert). Check path's named parameters syntax.");
        }
       
        
        $wildcards = array_map(function($wildcard) use ($path) {
            $filtered = array_filter($wildcard, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);

            if (is_null($filtered['type'])) {
                throw InvalidAssocArrayValueException::withMessage("Path: $path cannot be converted, `type` key is null for parameter: {$filtered['path_parameter']}. Check path's named parameters syntax.");
            }

            // if /:parameter then automatically set <slug> type
            if ($filtered['type'] === "") {
                $filtered['type'] = "<slug>";
            }
            return $filtered;
        }, $wildcards);

        foreach($wildcards as $wildcard) {
            $type = $wildcard['type'];
            $param_name = $wildcard['path_parameter'];
            if (!in_array($type, array_keys(self::PATH_PARAMETERS_TYPES))) {
                throw new InvalidAssocArrayValueException('type', array_keys(self::PATH_PARAMETERS_TYPES), $type);
            }
            switch ($type) {
                case '<slug>':
                    $path = preg_replace(
                        "#($type)?:$param_name#",
                        sprintf("(?P<%s>%s)", $param_name, self::PATH_PARAMETERS_TYPES[$type]),
                        $path
                    );
                    break;
                case '<string>':
                    $path = preg_replace(
                        "#$type:$param_name#",
                        sprintf("(?P<%s>%s)", $param_name, self::PATH_PARAMETERS_TYPES[$type]),
                        $path
                    );
                    break;
                case '<int>':
                    $path = preg_replace(
                        "#$type:$param_name#",
                        sprintf("(?P<%s>%s)", $param_name, self::PATH_PARAMETERS_TYPES[$type]),
                        $path
                    );
                    break;
            }
        }
        return $path;
    }

    public function getRoutesPathPatterns()
    {
        return array_keys(self::$routes);
    }

    public function getPathPatternFromPath($path)
    {
        return self::convertPathToPathPattern($path);
    }

    public function getControllerByPath($path)
    {
        $regexp = self::convertPathToPathPattern($path);
        return self::$routes[$regexp];
    }

    public function getControllerByPathPattern($path_pattern)
    {
        return self::$routes[$path_pattern];
    }

    private function parseRoute($path)
    {
        foreach (self::$routes as $regexp => $controller) {
            if (preg_match("#$regexp#", $path, $matches)) {

            }
            throw new \Exception("Route `$path` is not found", 404);
        }

    }

    private function prepareRoute(array $route): array
    {
        if (!isset($route['layout'])) {
            $route['layout'] = FRAMEWORK . "/Templates/Views/Errors/dev.php";
        }
        if (empty($route['action'])) {
            $route['action'] = 'index';
        }
        $route['controller'] = $this->upperCamelCase($route['controller']);
        return $route;
    }

    /**
     * @var string $route
     * @return array route params [...default_params, ...$concrete_params];
     * @throws \Exception
     */
    private function getRouteParams(string $route): array
    {
        foreach ($this->routes as $pattern => $default_params) {
            if (preg_match("#{$pattern}#", $route, $matches)) {
                $matches = array_filter($matches, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
                return [...$default_params, ...$matches];
            }
        }
        throw new \Exception("Route `$route` is not found", 404);
    }

    private function extractRouteFromQueryString(string $url): string
    {
        if ($url) {
            $route = explode("&", $url, 2)[0];
            if (strpos($route, "=") === false) {
                return rtrim($route, '/');
            }
        }
        return '';
    }

   

    private function upperCamelCase(string $str): string
    {
        return str_replace('-', '', ucwords($str, '-'));
    }


    public function __get(string $name): mixed
    {
        return $this->route[$name] ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->route[$name]);
    }

    public function toArray(): array
    {
        return $this->route;
    }
}