<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\Http\HttpFactory;
use Explt13\Nosmi\Interfaces\ExchangeInterface;
use Explt13\Nosmi\Interfaces\LightMiddlewareInterface;
use Explt13\Nosmi\Interfaces\LightRequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Interfaces\ReadExchangeInterface;
use Explt13\Nosmi\Interfaces\WriteExchangeInterface;

/**
 * Base class for middleware. \
 * All middleware must be extended from it.
 */
abstract class Middleware implements LightMiddlewareInterface
{
    private ?LightResponseInterface $early_response = null;
    
    /**
     * @param LightServerRequestInterface&ReadExchangeInterface&ExchangeInterface $request
     * @return LightServerRequestInterface&ReadExchangeInterface&ExchangeInterface
     */
    abstract protected function processRequest(LightServerRequestInterface $request): ?LightServerRequestInterface;
    
    /**
     * @param LightResponseInterface&ReadExchangeInterface&WriteExchangeInterface&ExchangeInterface $response
     * @return LightResponseInterface&ReadExchangeInterface&WriteExchangeInterface&ExchangeInterface
     */
    abstract protected function processResponse(LightResponseInterface $response, LightServerRequestInterface $request): LightResponseInterface;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): LightResponseInterface
    {
        $request = $this->processRequest($request);
        if (is_null($request)) {
            return $this->early_response;
        }
        $response = $handler->handle($request);
        $response = $this->processResponse($response, $request);
        return $response;
    }

    /**
     * @return LightResponseInterface&ReadExchangeInterface&WriteExchangeInterface&ExchangeInterface
     */
    final protected function createEarlyResponse(int $code = 200, string $reasonPhrase= ""): LightResponseInterface
    {
        $factory = new HttpFactory();
        $this->early_response = $factory->createResponse($code, $reasonPhrase);
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