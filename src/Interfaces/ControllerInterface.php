<?php
namespace Explt13\Nosmi\Interfaces;


interface ControllerInterface
{
    /**
     * Processes an incoming HTTP request and returns an appropriate response.
     *
     * @param LightServerRequestInterface $request The HTTP request to process.
     * @return LightResponseInterface The response generated after processing the request.
     */
    public function processRequest(LightServerRequestInterface $request): LightResponseInterface;
     
    /**
     * Sets the route information for the controller.
     *
     * @param LightRouteInterface $route The route to be set for the controller.
     */
    public function setRoute(LightRouteInterface $route);
}