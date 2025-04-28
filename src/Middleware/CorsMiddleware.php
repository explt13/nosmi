<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\Http\HttpFactory;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;


class CorsMiddleware extends Middleware
{
    
    public function processRequest(LightServerRequestInterface $request): ?LightServerRequestInterface
    {
        $method = strtolower($request->getMethod());
        if ($method === 'options') {
            $response = $this->createEarlyResponse();
            $this->earlyResponse($response->withCorsHeader());
            return null;
        }
        return $request;
    }

    public function processResponse(LightResponseInterface $response): LightResponseInterface
    {
        $response = $response->withCorsHeader();
        return $response;
    }
}