<?php

namespace Explt13\Nosmi\Interfaces;

use Psr\Http\Server\MiddlewareInterface;

interface LightRouteInterface
{
    /**
     * Resolves the given path against the defined routes.
     *
     * @param string $path The path to resolve.
     * @param string $method The method to resolve path for.
     * @return static returns self
     * @throws \RuntimeException if there is no route matched for a given path
     */
    public function resolvePath(string $path, string $method): static;

    /**
     * Adds a middleware for specific path pattern or path
     * @param string $path_pattern the path pattern to use a middleware for
     * @param MiddlewareInterface $middleware the middleware to add to the route
     * @return void;
     */
    public static function useMiddleware(string $path_pattern, MiddlewareInterface $middleware): void;

    /**
     * Disables a middleware for specific path pattern or path
     * @param string $path_pattern the path pattern to disable a middleware for
     * @param string $middleware_class the middleware class to disable
     * @return void;
     */
    public static function disableMiddleware(string $path_pattern, string $middleware_class): void;


    /**
     * Gets all middleware for the current route
     * @return array
     */
    public function getRouteMiddleware(): array;

    /**
     * Retrieves the controller associated with the resolved route.
     *
     * @return string The controller name.
     */
    public function getController(): string;

    /**
     * Retrieves the action page name that is associated with the resolved route.
     *
     * @return string The action name.
     */
    public function getAction(): ?string;

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
     * Adds a new GET route with a path pattern and associated controller.
     *
     * @param string $path_pattern The path pattern for the route.
     * @param string $controller The controller __class or interface name__ associated with the route. Controller must present in the dependency container.
     * @param ?string $action set a function for specified controller. If null is passed a standard HTTP method function is used (get, post, delete, put, patch).
     * @return void
     */
    public static function get(string $path_pattern, string $controller, ?string $action = null): void;

    /**
     * Adds a new POST route with a path pattern and associated controller.
     *
     * @param string $path_pattern The path pattern for the route.
     * @param string $controller The controller __class or interface name__ associated with the route. Controller must present in the dependency container.
     * @param ?string $action set a function for specified controller. If null is passed a standard HTTP method function is used (get, post, delete, put, patch).
     * @return void
     */
    public static function post(string $path_pattern, string $controller, ?string $action = null): void;

    /**
     * Adds a new DELETE route with a path pattern and associated controller.
     *
     * @param string $path_pattern The path pattern for the route.
     * @param string $controller The controller __class or interface name__ associated with the route. Controller must present in the dependency container.
     * @param ?string $action set a function for specified controller. If null is passed a standard HTTP method function is used (get, post, delete, put, patch).
     * @return void
     */
    public static function delete(string $path_pattern, string $controller, ?string $action = null): void;

    /**
     * Adds a new PUT route with a path pattern and associated controller.
     *
     * @param string $path_pattern The path pattern for the route.
     * @param string $controller The controller __class or interface name__ associated with the route. Controller must present in the dependency container.
     * @param ?string $action set a function for specified controller. If null is passed a standard HTTP method function is used (get, post, delete, put, patch).
     * @return void
     */
    public static function put(string $path_pattern, string $controller, ?string $action = null): void;

    /**
     * Adds a new PATCH route with a path pattern and associated controller.
     *
     * @param string $path_pattern The path pattern for the route.
     * @param string $controller The controller __class or interface name__ associated with the route. Controller must present in the dependency container.
     * @param ?string $action set a function for specified controller. If null is passed a standard HTTP method function is used (get, post, delete, put, patch).
     * @return void
     */
    public static function patch(string $path_pattern, string $controller, ?string $action = null): void;

    /**
     * Retrieves the mapping of path patterns to their corresponding regular expressions.
     * @param ?string $method get information for the specific HTTP method. If not specified it will return for all methods.
     * @return array An associative array mapping path patterns to regex patterns.
     */
    public static function getPatternToRegexMap(?string $method = null): array;

    /**
     * Retrieves all defined path patterns.
     * @param ?string $method get information for the specific HTTP method. If not specified it will return for all methods.
     * @return array An array of path patterns.
     */
    public static function getPathPatterns(?string $method = null): array;

    /**
     * Retrieves all defined path regular expressions.
     * @param ?string $method get information for the specific HTTP method. If not specified it will return for all methods.
     * @return array An array of regular expressions.
     */
    public static function getPathRegexps(?string $method = null): array;

    /**
     * Retrieves all defined routes.
     * @param ?string $method get information for the specific HTTP method. If not specified it will return for all methods.
     * @return array An associative array of routes.
     */
    public static function getRoutes(?string $method = null): array;

    /**
     * Retrieves the regular expression associated with a given path pattern.
     *
     * @param string $path_pattern The path pattern.
     * @param string $method get information for the specific HTTP method.
     * @return string|null The corresponding regular expression, or null if not found.
     */
    public static function getRegexpByPathPattern(string $path_pattern, string $method): ?string;

    /**
     * Retrieves the controller associated with a given path pattern.
     *
     * @param string $path_pattern The path pattern.
     * @param string $method get information for the specific HTTP method.
     * @return string|null The corresponding controller, or null if not path pattern found.
     */
    public static function getControllerByPathPattern(string $path_pattern, string $method): ?string;

    /**
     * Retrieves the action associated with a given path pattern.
     *
     * @param string $path_pattern The path pattern.
     * @param string $method get information for the specific HTTP method.
     * @return string|null The corresponding controller, or null if not path pattern found.
     */
    public static function getActionByPathPattern(string $path_pattern, string $method): ?string;

    /**
     * Retrieves the controller associated with a given regular expression.
     *
     * @param string $regexp The regular expression.
     * @param string $method get information for the specific HTTP method.
     * @return string|null The corresponding controller, or null if not found.
     */
    public static function getControllerByRegexp(string $regexp, string $method): ?string;

    /**
     * Retrieves all path patterns associated with a given controller.
     *
     * @param string $controller The controller name.
     * @param string $method get information for the specific HTTP method.
     * @return array An array of path patterns.
     */
    public static function getPathPatternsOfController(string $controller, string $method): array;

    /**
     * Retrieves an array of regular expressions associated with the specified controller.
     *
     * @param string $controller The name of the controller for which to retrieve the regular expressions.
     * @param string $method get information for the specific HTTP method.
     * @return array An array of regular expressions associated with the controller.
     */
    public static function getRegexpsOfController(string $controller, string $method): array;
}
