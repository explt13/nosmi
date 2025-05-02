<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\Interfaces\ControllerInterface;
use Explt13\Nosmi\Interfaces\LightRequestHandlerInterface;
use Explt13\Nosmi\Interfaces\MiddlewareFactoryInterface;

class MiddlewareFactory implements MiddlewareFactoryInterface
{
    public function createDispatcher(array $middleware_list, ControllerInterface $controller): LightRequestHandlerInterface
    {
        return new MiddlewareDispatcher($middleware_list, $controller);
    }

    public function createRegistry(): MiddlewareRegistry
    {
        return MiddlewareRegistry::getInstance();
    }
}