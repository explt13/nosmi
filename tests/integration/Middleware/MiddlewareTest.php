<?php

namespace Tests\Integration\Middleware;

use Explt13\Nosmi\Http\HttpFactory;
use Explt13\Nosmi\Middleware\AuthMiddleware;
use Explt13\Nosmi\Middleware\FinalHandler;
use Explt13\Nosmi\Middleware\FinalMiddleware;
use Explt13\Nosmi\Middleware\MiddlewareDispatcher;
use PHPUnit\Framework\TestCase;

class MiddlewareTest extends TestCase
{
    public function testDispatch()
    {
        $middleware_list = [
            new AuthMiddleware()
        ];
        $request = (new HttpFactory())->createServerRequest('method', 'fake/app/v1', $_SERVER);
        $middleware = new MiddlewareDispatcher($middleware_list, new FinalMiddleware());
        $response = $middleware->handle($request);

        $this->assertSame(['value1'], $response->getHeader('After-Auth'));
        $this->assertArrayNotHasKey('Authorization', $response->getHeaders());

    }
}