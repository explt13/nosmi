<?php

namespace Tests\Unit\Middleware;

use Explt13\Nosmi\Http\HttpFactory;
use Explt13\Nosmi\Interfaces\LightMiddlewareInterface;
use Explt13\Nosmi\Interfaces\LightRequestHandlerInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Middleware\AuthMiddleware;
use Explt13\Nosmi\Middleware\ErrorHandlerMiddleware;
use Explt13\Nosmi\Middleware\FinalHandler;
use Explt13\Nosmi\Middleware\FinalMiddleware;
use Explt13\Nosmi\Middleware\MiddlewareDispatcher;
use Explt13\Nosmi\Middleware\MiddlewareRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tests\Unit\helpers\Reset;

class MiddlewareTest extends TestCase
{
    private array $middleware_list;
    private LightMiddlewareInterface&MockObject $final_middleware;
    private LightRequestHandlerInterface&MockObject $middleware_dispatcher;
    private LightServerRequestInterface&MockObject $mock_server_request;
    public function setUp(): void
    {
        $mockResponse = $this->createMock(LightResponseInterface::class);
        $mockResponse->method('getHeader')->willReturnCallback(function($name) {
            return match ($name) {
                'auth' => ['token [123]'],
                'final' => ["123"],
            };
        });

        $mockMiddleware1 = $this->createMockMiddleware();
        $mockMiddleware2 = $this->createMockMiddleware();
        $final_middleware = $this->createMock(LightMiddlewareInterface::class);
        
        $final_middleware->method('process')->willReturnCallback(function(ServerRequestInterface $request, RequestHandlerInterface $handler) use ($mockResponse) {
                $mockResponse->withHeader('final', '123');
                return $mockResponse;
            }
        );


        $this->mock_server_request = $this->getMockBuilder(LightServerRequestInterface::class)->setConstructorArgs(["GET", "fake/app/v1", []])->getMock();

        $middleware_list = [];
        $middleware_list[] = $mockMiddleware1;
        $middleware_list[] = $mockMiddleware2;

        $this->middleware_dispatcher = $this->getMockBuilder(LightRequestHandlerInterface::class)->setConstructorArgs([$middleware_list, $final_middleware])->getMock();
        $this->middleware_dispatcher->method('handle')->willReturnCallback(function($request) use (&$middleware_list, $final_middleware) {
            if (empty($middleware_list)) {
                return $final_middleware->process($request, $this->middleware_dispatcher);
            }
            $m = array_shift($middleware_list);
            $response = $m->process($request, $this->middleware_dispatcher);
            return $response;
        });
    }
    public function testMiddlewareDispatcherHandlesResponse()
    {
        $response = $this->middleware_dispatcher->handle($this->mock_server_request);

        $this->assertSame(['token [123]'], $response->getHeader('auth'));
        $this->assertSame(['123'], $response->getHeader('final'));
    }

    private function createMockMiddleware(): LightMiddlewareInterface&MockObject
    {
        $mockMiddleware = $this->createMock(LightMiddlewareInterface::class);
        $mockMiddleware->method('process')->willReturnCallback(function(ServerRequestInterface $request, RequestHandlerInterface $handler) {
            $response = $handler->handle($request);
            return $response;
        });
        return $mockMiddleware;
    }

    public static function tearDownAfterClass(): void
    {
        Reset::resetSingleton(MiddlewareRegistry::class);
    }
}