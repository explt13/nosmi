<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;

class AuthorizationMiddleware extends Middleware
{
    protected function processRequest(LightServerRequestInterface $request): ?LightServerRequestInterface
    {
        $auth = $request->getHeaderLine('Authorization');
        if (empty($auth)) {
            $this->reject(401);
            return null;
        }
        return $request;
    }

    protected function processResponse(LightResponseInterface $response): LightResponseInterface
    {
        return $response;
    }
}