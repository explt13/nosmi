<?php

namespace Explt13\Nosmi\Middleware;

use Explt13\Nosmi\Base\ErrorHandler;
use Explt13\Nosmi\Http\HttpErrorHandler;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ErrorHandlerMiddleware extends Middleware
{
    protected function processRequest(LightServerRequestInterface $request): ?LightServerRequestInterface
    {
        return $request;
    }

    protected function processResponse(LightResponseInterface $response, LightServerRequestInterface $request): LightResponseInterface
    {
        return $response;
    }

   
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): LightResponseInterface
    {
        try {
            return parent::process($request, $handler);
        } catch (\Throwable $e) {
            $handler = new HttpErrorHandler($request);
            return $handler->exceptionHandler($e);
        }
    }
}