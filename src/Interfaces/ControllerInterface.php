<?php
namespace Explt13\Nosmi\Interfaces;


interface ControllerInterface
{
    /**
     * Processes an incoming HTTP request and returns an appropriate response.
     *
     * @param LightServerRequestInterface&ReadExchangeInterface&ExchangeInterface $request The HTTP request to process.
     * @return LightResponseInterface&ReadExchangeInterface&WriteExchangeInterface&ExchangeInterface The response generated after processing the request.
     */
    public function processRequest(LightServerRequestInterface $request): LightResponseInterface;
     
    /**
     * Sets the route information for the controller.
     *
     * @param LightRouteInterface $route The route to be set for the controller.
     */
    public function setRoute(LightRouteInterface $route): void;

    /**
     * Sets the response for the controller.
     *
     * @param LightResponseInterface&ReadExchangeInterface&WriteExchangeInterface&ExchangeInterface $route The response to be set for the controller.
     */
    public function setResponse(LightResponseInterface $response): void;

/**
     * Creates the client for the controller.
     *
     * @param LightClientInterface $route The client to be set for the controller.
     */
    public function setClient(LightClientInterface $client): void;
}