<?php

namespace Explt13\Nosmi\Interfaces;

interface ControllerFactoryInterface
{
    /**
     * Creates a controller instance based on the given request and route.
     *
     * @param LightRouteInterface $route The route object.
     * @return ControllerInterface The created controller instance.
     */
    public function createController(LightRouteInterface $route): ControllerInterface;

}