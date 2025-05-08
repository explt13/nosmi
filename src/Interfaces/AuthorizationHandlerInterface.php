<?php

namespace Explt13\Nosmi\Interfaces;

interface AuthorizationHandlerInterface
{
    public function isValid(string $token): bool;
}