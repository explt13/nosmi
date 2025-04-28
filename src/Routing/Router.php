<?php
namespace Explt13\Nosmi\Routing;

use Explt13\Nosmi\Base\RequestPipeline;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Middleware\MiddlewareFactory;

class Router
{
    private LightRouteInterface $route;
    private RequestPipeline $request_pipeline;

    public function __construct(
        RequestPipeline $request_pipeline,
        LightRouteInterface $route,
    )
    {
        $this->request_pipeline = $request_pipeline;
        $this->route = $route;
    }

    public function dispatch(LightServerRequestInterface $request): void
    {
        if (empty($this->route::getRoutes())) {
            throw new \LogicException('No routes found, make sure you added them correctly.');
        }
        $path = $request->getUri()->getPath();
        $this->route = $this->route->resolvePath($path);
        $this->request_pipeline->process($request, $this->route);
    }
}