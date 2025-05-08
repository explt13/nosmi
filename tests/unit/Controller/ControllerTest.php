<?php

namespace Tests\Unit\Controller;

use Explt13\Nosmi\Base\Controller;
use Explt13\Nosmi\Http\Response as HttpResponse;
use Explt13\Nosmi\Interfaces\ControllerInterface;
use Explt13\Nosmi\Interfaces\LightResponseInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Interfaces\LightServerRequestInterface;
use Explt13\Nosmi\Interfaces\ReadExchangeInterface;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ControllerTest extends TestCase
{
    private ControllerInterface $controller;
    private LightResponseInterface&ReadExchangeInterface&MockObject $responseMock;
    private LightRouteInterface&MockObject $routeMock;
    private LightServerRequestInterface&MockObject $requestMock;

    public function setUp(): void
    {
        $this->routeMock = $this->createMock(LightRouteInterface::class);
        $this->routeMock->method('getPath')->willReturn('api/some/route');
        $this->routeMock->method('getController')->willReturn('NewController');

        $this->requestMock = $this->createMock(LightServerRequestInterface::class);
        $this->responseMock = $this->createMockForIntersectionOfInterfaces([ReadExchangeInterface::class, LightResponseInterface::class]);
        $this->responseMock->method('getBodyContent')->willReturn('123');
        $this->responseMock->method('getStatusCode')->willReturn(201);
        $this->controller = $this->createController();
        $this->controller->setRoute($this->routeMock);
    }

    public function testSetRoute()
    {
        $controllerReflection = new \ReflectionClass($this->controller::class);
        $getRouteReflection = $controllerReflection->getMethod('getRoute');
        $getRouteReflection->setAccessible(true);
        $this->assertSame($this->routeMock, $getRouteReflection->invoke($this->controller));
    }

    public function testProcessAjaxRequest()
    {
        $this->requestMock->method('isAjax')->willReturn(true);
        $this->requestMock->method('getMethod')->willReturnOnConsecutiveCalls('GET', 'nonStandardAjaxMethod');
        
        $response = $this->controller->processRequest($this->requestMock);
        $this->assertSame('123', $response->getBodyContent());

        $response = $this->controller->processRequest($this->requestMock);
        $this->assertSame(201, $response->getStatusCode());
    }

    public function testProcessAjaxRequestThrowsExceptionIfStandardMethodIsNotImplemented()
    {
        $this->requestMock->method('isAjax')->willReturn(true);
        $this->requestMock->method('getMethod')->willReturn('POST');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Method POST is not available for the route: api/some/route");
        $this->controller->processRequest($this->requestMock);
    }

    public function testProcessAjaxRequestThrowsExceptionIfNonStandardMethodIsNotImplemented()
    {
        $this->requestMock->method('isAjax')->willReturn(true);
        $this->requestMock->method('getMethod')->willReturn('NON-STANDARD-METHOD-THAT-IS-NOT-IMPLEMENTED');
        $this->requestMock->method('isAjax')->willReturn(true);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Route api/some/route does not have non-standard-method-that-is-not-implemented method.");
        $this->controller->processRequest($this->requestMock);
    }

    public function testProcessNonAjaxRequest()
    {
        $this->requestMock->method('isAjax')->willReturn(false);
        $this->routeMock->method('getAction')->willReturn('some');
        $response = $this->controller->processRequest($this->requestMock);
        $this->assertSame(201, $response->getStatusCode());
    }

    public function testProcessNonAjaxRequestThrowsOnUndefinedMethod()
    {
        $this->requestMock->method('isAjax')->willReturn(false);
        $this->routeMock->method('getAction')->willReturn('NotExisted');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Expected controller NewController to have notexistedAction method.");
        $this->controller->processRequest($this->requestMock);
    }

    public function testProcessNonAjaxRequestThrowsOnNull()
    {
        $this->requestMock->method('isAjax')->willReturn(false);
        $this->routeMock->method('getAction')->willReturn(null);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Route api/some/route does not have an action. If the route is not assumed to be an API only, provide an action in Route::add method.");
        $this->controller->processRequest($this->requestMock);
    }
    
    
    private function createController(): ControllerInterface
    {
        return new class($this->responseMock) extends Controller {
            public function __construct(protected LightResponseInterface $response) {
                $this->response = $response;
            }

            protected function get(): LightResponseInterface
            {
                return $this->response;
            }

            protected function nonStandardAjaxMethod(): LightResponseInterface
            {
                return $this->response;
            }

            protected function someAction(): LightResponseInterface
            {
                return $this->response;
            }
        };
    }
}