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
        return array_search($this->regexp, self::$patterns_map);
    }
    
    public static function add(string $path_pattern, string $controller, ?string $action = null): void
    {
        if (!class_exists($controller) && !is_subclass_of($controller, Controller::class)) {
            throw new \RuntimeException("Expected controller to be an actual class name, got: $controller");
        }
        $regexp = PathConvertter::convertPathPatternToRegexp($path_pattern);
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

    private function setPathParams(array $parameters): void
    {
        foreach ($parameters as $name => $value) {
            $this->params[$name] = $value;
        }
    }
}