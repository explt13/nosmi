<?php

namespace Explt13\Nosmi\Interfaces;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface LightRequestHandlerInterface extends RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): LightResponseInterface;
}