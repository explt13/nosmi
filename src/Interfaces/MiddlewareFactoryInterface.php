<?php
namespace Explt13\Nosmi\Interfaces;

use Explt13\Nosmi\Middleware\MiddlewareRegistry;

interface MiddlewareFactoryInterface
{
    public function createDispatcher(array $middleware_list, ControllerInterface $controller): LightRequestHandlerInterface;
    public function createRegistry(): MiddlewareRegistry;
}