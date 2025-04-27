<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\Http\HttpFactory;
use Explt13\Nosmi\Http\Response;
use Explt13\Nosmi\Interfaces\LightMiddlewareInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Nyholm\Psr7\Response as Psr7Response;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class FinalMiddleware implements LightMiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): LightResponseInterface
    {
        $request = $request->withHeader('FINAL_HEADER', 'FINAL');
        $p = "213";
        $factory = new HttpFactory();
        $response = $factory->createResponse(201);
        $response->getBody()->write('Middleware final dest');
        return $response;
    }
}