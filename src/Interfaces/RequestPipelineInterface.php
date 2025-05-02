<?php

namespace Explt13\Nosmi\Interfaces;

interface RequestPipelineInterface
{
    
    /**
     * Processes the given server request and route to produce a response.
     *
     * @param LightServerRequestInterface $request The server request to be processed.
     * @param LightRouteInterface $route The route associated with the request.
     * 
     * @return LightResponseInterface The response generated after processing the request and route.
     */
    public function process(LightServerRequestInterface $request, LightRouteInterface $route): LightResponseInterface;
}