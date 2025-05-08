<?php

namespace Explt13\Nosmi\Interfaces;

interface RouterInterface
{
    /**
     * Resolves the given server request and returns the corresponding route.
     *
     * @param LightServerRequestInterface&ReadExchangeInterface&ExchangeInterface $request The server request to resolve.
     * @return LightRouteInterface The resolved route.
     */
    public function resolve(LightServerRequestInterface $request): LightRouteInterface;
}