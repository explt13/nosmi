<?php

namespace Explt13\Nosmi\Interfaces;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface LightRequestHandlerInterface extends RequestHandlerInterface
{
    /**
     * @param LightServerRequestInterface&ReadExchangeInterface&ExchangeInterface $request The server request to be processed.
     */
    public function handle(ServerRequestInterface $request): LightResponseInterface;
}