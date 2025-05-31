<?php

namespace Explt13\Nosmi\Routing;

use Explt13\Nosmi\Base\Controller;
use Explt13\Nosmi\Exceptions\InvalidAssocArrayValueException;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\MiddlewareRegistryInterface;
use Explt13\Nosmi\Utils\PathConvertter;
use Psr\Http\Server\MiddlewareInterface;

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
    protected static MiddlewareRegistryInterface $middleware_registry;

    public function __construct(MiddlewareRegistryInterface $middleware_registry)
    {
        self::$middleware_registry = $middleware_registry;
    }
    public function resolvePath(string $path, string $method): static
    {
        foreach (self::$routes[$method] as $regexp => $specs) {
            if (preg_match("#$regexp#", $path, $parameters)) {
                $parameters = array_filter($parameters, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);
                $new = clone $this;
                $new->setPathParams($parameters);
                $new->path = $path;
                $new->regexp = $regexp;
                $new->setPathPattern($method);
                $new->controller = $specs['controller'];
                $new->action = $new->checkAction($specs['action']);
                return $new;
            }
        }
        throw new \RuntimeException("Route `$path` is not found", 404);
    }

    public static function useMiddleware(string $path_pattern, MiddlewareInterface $middleware): void
    {
        $regexp = PathConvertter::convertPathPatternToRegexp($path_pattern);
        self::$middleware_registry->add($middleware, $regexp);
    }

    public static function disableMiddleware(string $path_pattern, string $middleware_class): void
    {
        $regexp = PathConvertter::convertPathPatternToRegexp($path_pattern);
        self::$middleware_registry->remove($middleware_class, $regexp);
    }

    public function getRouteMiddleware(): array
    {
        return self::$middleware_registry->getForRoute($this->getPath());
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
        return $this->path_pattern;
    }
    
    public static function get(string $path_pattern, string $controller, ?string $action = null): void
    {
        self::addRoute('GET', $path_pattern, $controller, $action);
    }
   
    public static function post(string $path_pattern, string $controller, ?string $action = null): void
    {
        self::addRoute('POST', $path_pattern, $controller, $action);
    }

    public static function delete(string $path_pattern, string $controller, ?string $action = null): void
    {
        self::addRoute('DELETE', $path_pattern, $controller, $action);
    }

    public static function put(string $path_pattern, string $controller, ?string $action = null): void
    {
        self::addRoute('PUT', $path_pattern, $controller, $action);
    }

    public static function patch(string $path_pattern, string $controller, ?string $action = null): void
    {
        self::addRoute('PATCH', $path_pattern, $controller, $action);
    }

    public static function getPatternToRegexMap(?string $method = null): array
    {
        if (is_null($method)) {
            return self::$patterns_map;
        }
        return self::$patterns_map[$method] ?? [];
    }

    public static function getPathPatterns(?string $method = null): array
    {
        if (is_null($method)) {
            $all = [];
            foreach (self::$patterns_map as $m => $patterns) {
                foreach ($patterns as $pattern => $regexp) {
                    $all[] = $pattern;
                }
            }
            return $all;
        }
        return isset(self::$patterns_map[$method]) ? array_keys(self::$patterns_map[$method]) : [];
    }

    public static function getPathRegexps(?string $method = null): array
    {
        if (is_null($method)) {
            $all = [];
            foreach (self::$patterns_map as $m => $patterns) {
                foreach ($patterns as $pattern => $regexp) {
                    $all[] = $regexp;
                }
            }
            return $all;
        }
        return isset(self::$patterns_map[$method]) ? array_values(self::$patterns_map[$method]) : [];
    }

    public static function getRoutes(?string $method = null): array
    {
        if (is_null($method)) {
            return self::$routes;
        }
        return self::$routes[$method] ?? [];
    }

    public static function getRegexpByPathPattern(string $path_pattern, string $method): ?string
    {
        return self::$patterns_map[$method][$path_pattern] ?? null;
    }

    public static function getControllerByPathPattern(string $path_pattern, string $method): ?string
    {
        $regexp = self::getRegexpByPathPattern($path_pattern, $method);
        if (is_null($regexp)) return null;
        return self::$routes[$method][$regexp]['controller'] ?? null;
    }

    public static function getActionByPathPattern(string $path_pattern, string $method): ?string
    {
        $regexp = self::getRegexpByPathPattern($path_pattern, $method);
        if (is_null($regexp)) return null;
        return self::$routes[$method][$regexp]['action'] ?? null;
    }

    public static function getControllerByRegexp(string $regexp, string $method): ?string
    {
        return self::$routes[$method][$regexp]['controller'] ?? null;
    }

    public static function getPathPatternsOfController(string $controller, string $method): array
    {
        $regexps = self::getRegexpsOfController($controller, $method);
        $patterns = [];
        foreach (self::$patterns_map[$method] ?? [] as $pattern => $regexp) {
            if (in_array($regexp, $regexps, true)) {
                $patterns[] = $pattern;
            }
        }
        return $patterns;
    }

    public static function getRegexpsOfController(string $controller, string $method): array
    {
        $routes = array_filter(self::$routes[$method] ?? [], function($route) use ($controller) {
            return $route['controller'] === $controller;
        });
        return array_keys($routes);
    }

    private function checkAction(?string $action): ?string
    {
        if (is_null($action)) {
            return null;
        }
        if (preg_match("/^[a-zA-Z0-9_]+$/", $action)) {
            return $action;
        }
        throw new \LogicException("Route {$this->path}: `action` parameter should have ^[a-zA-Z0-9_]+$ pattern.");
    }

    private function setPathParams(array $parameters): void
    {
        foreach ($parameters as $name => $value) {
            $this->params[$name] = $value;
        }
    }

    private function setPathPattern(string $method): void
    {
        $this->path_pattern = array_search($this->regexp, self::$patterns_map[$method]);
    }

    private static function addRoute(string $method, string $path_pattern, string $controller, ?string $action): void
    {
        self::validateControllerExistence($controller);
        $regexp = PathConvertter::convertPathPatternToRegexp($path_pattern);
        self::$patterns_map[$method][$path_pattern] = $regexp;
        self::$routes[$method][$regexp] = ['controller' => $controller, 'action' => $action];
    }

    private static function validateControllerExistence(string $controller): void
    {
        if (!class_exists($controller) && !is_subclass_of($controller, Controller::class)) {
            throw new \RuntimeException("Expected controller to be an actual class name, got: $controller");
        }
    }
}