<?php

namespace Explt13\Nosmi\Interfaces;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface LightMiddlewareInterface extends MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): LightResponseInterface;
}