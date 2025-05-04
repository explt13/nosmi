<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\Http\HttpFactory;
use Explt13\Nosmi\Interfaces\LightMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;

/**
 * Base class for middleware. \
 * All middleware must be extended from it.
 */
abstract class Middleware implements LightMiddlewareInterface
{
    private ?LightResponseInterface $early_response = null;
    abstract protected function processRequest(LightServerRequestInterface $request): ?LightServerRequestInterface;
    abstract protected function processResponse(LightResponseInterface $response): LightResponseInterface;

    /**
     * @param LightRequestHandlerInterface $handler
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): LightResponseInterface
    {
        $request = $this->processRequest($request);
        if (is_null($request)) {
            return $this->early_response;
        }
        $response = $handler->handle($request);
        $response = $this->processResponse($response);
        return $response;
    }

    final protected function createEarlyResponse(): LightResponseInterface
    {
        $factory = new HttpFactory();
        $this->early_response = $factory->createResponse();
        return $this->early_response;
    }

    final protected function earlyResponse(LightResponseInterface $response): void
    {
        $this->early_response = $response;
    }

    final protected function reject(int $code, string $reasonPhrase = "")
    {
        $factory = new HttpFactory();
        $this->early_response = $factory->createResponse($code, $reasonPhrase);
    }
}