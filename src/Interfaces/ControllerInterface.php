<?php
namespace Explt13\Nosmi\Interfaces;


interface ControllerInterface
{
    public function processRequest(LightServerRequestInterface $request): LightResponseInterface;
}