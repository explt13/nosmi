<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\Http\HttpFactory;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;


class CorsMiddleware extends Middleware
{
    
    protected function processRequest(LightServerRequestInterface $request): ?LightServerRequestInterface
    {
        if ($request->isOptions()) {
            $response = $this->createEarlyResponse();
            $this->earlyResponse($response->withCorsHeader()->withEmpty());
            return null;
        }
        return $request;
    }

    protected function processResponse(LightResponseInterface $response, LightServerRequestInterface $request): LightResponseInterface
    {
        $response = $response->withCorsHeader();
        return $response;
    }
}