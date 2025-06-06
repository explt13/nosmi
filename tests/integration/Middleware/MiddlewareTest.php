<?php

namespace Tests\Integration\Middleware;

use Explt13\Nosmi\Base\Controller;
use Explt13\Nosmi\Http\HttpFactory;
use Explt13\Nosmi\Middleware\MiddlewareDispatcher;
use PHPUnit\Framework\TestCase;

class MiddlewareTest extends TestCase
{
    public function testDispatch()
    {
        $middleware_list = [
            new AuthMiddleware(),
            new FinalMiddleware()
        ];
        $request = (new HttpFactory())->createServerRequest('method', 'fake/app/v1', $_SERVER);
        $middleware = new MiddlewareDispatcher($middleware_list, new class extends Controller{});
        $response = $middleware->handle($request);

        $this->assertSame(['value1'], $response->getHeader('After-Auth'));
        $this->assertArrayNotHasKey('Authorization', $response->getHeaders());

    }
}