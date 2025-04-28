<?php

namespace Tests\Integration\Middleware;

use Explt13\Nosmi\Interfaces\LightMiddlewareInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class AuthMiddleware implements LightMiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): LightResponseInterface
    {
        $request = $request->withHeader('Authorization', 'Bearer 123123');
        $response = $handler->handle($request);
        $response = $response->withHeader('After-Auth', 'value1');
        return $response;
    }
}