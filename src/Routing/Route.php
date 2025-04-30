<?php

namespace Explt13\Nosmi\Routing;

use Explt13\Nosmi\Base\Controller;
use Explt13\Nosmi\Exceptions\InvalidAssocArrayValueException;
use Explt13\Nosmi\Interfaces\LightRouteInterface;

class Route implements LightRouteInterface
{
    /**
     * @var string[] $params route parameters array
     */
    protected array $params = [];
    protected string $controller;
    protected string $regexp;
    protected string $path;
    protected string $path_pattern;
    protected ?string $action;
    protected static array $routes = [];
    protected static array $patterns_map = [];
    protected static array $route_middleware = [];
    protected const PATH_PARAMETERS_TYPES = ['<string>' => '[a-zA-Z]+', '<int>' => '[0-9]+', '<slug>' => '[a-zA-Z0-9-]+'];

    public function resolvePath(string $path): static
    {
        foreach (self::$routes as $regexp => $specs) {
            if (preg_match("#$regexp#", $path, $parameters)) {
                $parameters = array_filter($parameters, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
                $new = clone $this;
                $new->setPathParams($parameters);
                $new->path = $path;
                $new->regexp = $regexp;
                $new->path_pattern = $new->getPathPattern();
                $new->controller = $specs['controller'];
                $new->action = $new->checkAction($specs['action']);
                return $new;
            }
        }
        throw new \RuntimeException("Route `$path` is not found", 404);
    }

    public static function useMiddleware(string $path_pattern, string $middleware_class): void
    {
        if (!isset(self::$patterns_map[$path_pattern])) {
            throw new \RuntimeException("Path pattern: $path_pattern is not found in patterns map");
        }
        self::$route_middleware[$path_pattern][] = $middleware_class;
    }

    public function getRouteMiddleware(): array
    {
        return self::$route_middleware[$this->path_pattern];
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $param): ?string
    {
        return $this->params[$param] ?? null;
    }
    
    public function getPathRegexp(): string
    {
        return $this->regexp;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getPathPattern(): string
    {
        return array_search($this->regexp, self::$patterns_map);
    }
    
    public static function add(string $path_pattern, string $controller, ?string $action = null): void
    {
        $regexp = self::convertPathPatternToRegexp($path_pattern);
        self::$patterns_map[$path_pattern] = $regexp;
        self::$routes[$regexp] = ['controller' => $controller, 'action' => $action];
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

    public static function getRegexpByPathPattern(string $path_pattern): ?string
    {
        return isset(self::$patterns_map[$path_pattern]) ? self::$patterns_map[$path_pattern] : null;
    }

    public static function getControllerByPathPattern(string $path_pattern): ?string
    {
        $regexp = self::getRegexpByPathPattern($path_pattern);
        if (is_null($regexp)) return null;
        return self::$routes[$regexp]['controller'];
    }

    public static function getActionByPathPattern(string $path_pattern): ?string
    {
        $regexp = self::getRegexpByPathPattern($path_pattern);
        if (is_null($regexp)) return null;
        return self::$routes[$regexp]['action'];
    }

    public static function getControllerByRegexp(string $regexp): ?string
    {
        return isset(self::$routes[$regexp]) ? self::$routes[$regexp]['controller'] : null;
    }

    public static function getPathPatternsOfController(string $controller): array
    {
        $regexps = self::getRegexpsOfController($controller);
        $patterns = array_keys(array_filter(self::$patterns_map, function($regexp) use ($regexps) {
            return in_array($regexp, $regexps, true);
        }));
        return $patterns;
    }

    public static function getRegexpsOfController(string $controller): array
    {
        $routes = array_filter(self::$routes, function($route) use ($controller) {
            return $route['controller'] === $controller;
        });
        return array_keys($routes);
    }

    private function checkAction(?string $action): ?string
    {
        if (is_null($action)) {
            return null;
        }
        if (preg_match("/^[a-z0-9]*$/", $action)) {
            return $action;
        }
        throw new \LogicException("Route {$this->path}: `action` parameter should have ^[a-z0-9]*$ pattern.");
    }


    private static function convertPathPatternToRegexp(string $path_pattern): string
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

    private function setPathParams(array $parameters): void
    {
        foreach ($parameters as $name => $value) {
            $this->params[$name] = $value;
        }
    }
}