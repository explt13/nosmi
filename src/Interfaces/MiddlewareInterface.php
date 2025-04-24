<?php

namespace Explt13\Nosmi\Interfaces;

interface MiddlewareInterface
{
    public function processRequest(LightRequestInterface $request);
    public function processResponse(LightResponseInterface $response);
    public function run();
}