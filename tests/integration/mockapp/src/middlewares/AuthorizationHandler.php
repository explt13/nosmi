<?php

namespace Tests\Integration\mockapp\src\middlewares;

use Explt13\Nosmi\Interfaces\AuthorizationHandlerInterface;

class AuthorizationHandler implements AuthorizationHandlerInterface
{
    public function isValid(string $token): bool
    {
        return true;
    }
}