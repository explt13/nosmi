<?php
namespace Explt13\Nosmi\Routing;

use Explt13\Nosmi\Base\RequestPipeline;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Interfaces\RouterInterface;
use Explt13\Nosmi\Middleware\MiddlewareFactory;

class Router implements RouterInterface
{
    private LightRouteInterface $route;

    public function __construct(
        LightRouteInterface $route,
    )
    {
        $this->route = $route;
    }

    public function resolve(LightServerRequestInterface $request): LightRouteInterface
    {
        if (empty($this->route::getRoutes())) {
            throw new \LogicException('No routes found, make sure you added them correctly.');
        }
        $path = $request->getUri()->getPath();
        return $this->route->resolvePath($path);
    }
}