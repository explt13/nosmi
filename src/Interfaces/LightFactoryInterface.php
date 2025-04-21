<?php

namespace Explt13\Nosmi\Interfaces;

interface LightFactoryInterface
{
    public function createController(LightRequestInterface $request, LightRouteInterface $route): ControllerInterface;
    public function createRequest(string $method, string $uri): LightRequestInterface;
    public function createResponse(int $status = 200): LightResponseInterface;
    public function createView(LightRouteInterface $route): ViewInterface;
    public function createModel(): ModelInterface;
}