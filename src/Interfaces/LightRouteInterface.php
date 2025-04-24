<?php

namespace Explt13\Nosmi\Interfaces;

interface LightRouteInterface
{
    /**
     * Resolves the given path against the defined routes.
     *
     * @param string $path The path to resolve.
     * @return bool True if the path matches a route, false otherwise.
     */
    public function resolvePath(string $path): bool;

    /**
     * Retrieves the controller associated with the resolved route.
     *
     * @return string The controller name.
     */
    public function getController(): string;

    /**
     * Retrieves all parameters from the resolved route.
     *
     * @return array An associative array of parameters.
     */
    public function getParams(): array;

    /**
     * Retrieves a specific parameter by name from the resolved route.
     *
     * @param string $param The name of the parameter.
     * @return string|null The parameter value, or null if not found.
     */
    public function getParam(string $param): ?string;

    /**
     * Retrieves the regular expression pattern for the route path.
     *
     * @return string The regular expression pattern.
     */
    public function getPathRegexp(): string;

    /**
     * Retrieves the original path for the route.
     *
     * @return string The path pattern.
     */
    public function getPath(): string;

     /**
     * Retrieves the route path pattern.
     *
     * @return string The path pattern.
     */
    public function getPathPattern(): string;

    /**
     * Adds a new route with a path pattern and associated controller.
     *
     * @param string $path_pattern The path pattern for the route.
     * @param string $controller The controller associated with the route.
     * @return void
     */
    public static function add(string $path_pattern, string $controller): void;

    /**
     * Retrieves the mapping of path patterns to their corresponding regular expressions.
     *
     * @return array An associative array mapping path patterns to regex patterns.
     */
    public static function getPatternToRegexMap(): array;

    /**
     * Retrieves all defined path patterns.
     *
     * @return array An array of path patterns.
     */
    public static function getPathPatterns(): array;

    /**
     * Retrieves all defined path regular expressions.
     *
     * @return array An array of regular expressions.
     */
    public static function getPathRegexps(): array;

    /**
     * Retrieves all defined routes.
     *
     * @return array An associative array of routes.
     */
    public static function getRoutes(): array;

    /**
     * Retrieves the regular expression associated with a given path pattern.
     *
     * @param string $path_pattern The path pattern.
     * @return string|null The corresponding regular expression, or null if not found.
     */
    public static function getRegexpByPathPattern(string $path_pattern): ?string;

    /**
     * Retrieves the controller associated with a given path pattern.
     *
     * @param string $path_pattern The path pattern.
     * @return string|null The corresponding controller, or null if not found.
     */
    public static function getControllerByPathPattern(string $path_pattern): ?string;

    /**
     * Retrieves the controller associated with a given regular expression.
     *
     * @param string $regexp The regular expression.
     * @return string|null The corresponding controller, or null if not found.
     */
    public static function getControllerByRegexp(string $regexp): ?string;

    /**
     * Retrieves all path patterns associated with a given controller.
     *
     * @param string $controller The controller name.
     * @return array An array of path patterns.
     */
    public static function getControllerPatternPaths(string $controller): array;
    /**
     * Retrieves an array of regular expressions associated with the specified controller.
     *
     * @param string $controller The name of the controller for which to retrieve the regular expressions.
     * @return array An array of regular expressions associated with the controller.
     */
    public static function getControllerRegexps(string $controller): array;
}
