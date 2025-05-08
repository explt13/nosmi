<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\Interfaces\AuthorizationHandlerInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;

class AuthorizationMiddleware extends Middleware
{
    public function __construct(private AuthorizationHandlerInterface $authorization)
    {
        
    }
    protected function processRequest(LightServerRequestInterface $request): ?LightServerRequestInterface
    {
        $auth_header = $request->getHeaderLine('Authorization');

        if (!preg_match('/^Bearer\s+(.+)$/i', $auth_header, $matches)) {
            $this->reject(401);
            return null;
        }
        
        $token = trim($matches[1]);

        if (!$this->authorization->isValid($token)) {
            $this->reject(403);
            return null;
        }
        return $request;
    }

    protected function processResponse(LightResponseInterface $response): LightResponseInterface
    {
        return $response;
    }
}