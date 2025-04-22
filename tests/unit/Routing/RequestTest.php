<?php

namespace Tests\Unit\Routing;
use PHPUnit\Framework\TestCase;
use Explt13\Nosmi\Routing\Request;
use Explt13\Nosmi\Exceptions\NotInArrayException;
use Nyholm\Psr7\Factory\Psr17Factory;


class RequestTest extends TestCase
{
    public function testInitCreatesRequestFromGlobals()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['SERVER_PORT'] = '7777';
        $_SERVER['REQUEST_URI'] = '/test';

        $request = Request::init();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('https://example.com:7777/test', (string) $request->getUri());
    }

    public function testReturnsUriWithoutPortIfUriIsDefaultForScheme()
    {
        $this->assertEquals('https://something.com/route', (string) (new Request('GET', 'https://something.com:443/route'))->getUri());
        $this->assertEquals('http://something.com/route', (string) (new Request('GET', 'http://something.com:80/route'))->getUri());
    }

    public function testGetHeaderReturnsCorrectHeader()
    {
        $_SERVER['HTTP_CUSTOM_HEADER'] = 'CustomValue';

        $request = new Request('GET', 'http://example.com');
        $this->assertEquals(['CustomValue'], $request->getHeader('CUSTOM-HEADER'));
    }

    public function testGetParsedBodyParsesJsonBody()
    {
        $request = $this->createRequestWithStream(['key' => 'value']);

        $parsedBody = $request->getParsedBody();
        $this->assertEquals(['key' => 'value'], $parsedBody);
    }

    public function testValidateThrowsExceptionForMissingRequiredField()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('{"name":"name is required."}');

        $request = new Request('GET', 'http://example.com');
        $data = $request->getQueryParams();
        $data = $request->validate($data, ['name' => 'required']);
    }

    public function testValidateReturnsDataWhenValid()
    {
        $request = new Request('GET', 'http://example.com?name=John');
        $data = $request->getQueryParams();
        $data = $request->validate($data, ['name' => 'required']);

        $this->assertEquals(['name' => 'John'], $data);
    }

    public function testValidateReturnsDataWhenValidFromStream()
    {
        $request = $this->createRequestWithStream(['something' => 'else']);
        $data = $request->getParsedBody();
        $data = $request->validate($data, ['something' => 'required']);
        $this->assertEquals(['something' => 'else'], $data);
    }

    public function testValidateIsAllowedMethodThrowsExceptionForInvalidMethod()
    {
        $this->expectException(NotInArrayException::class);

        new Request('INVALID', 'http://example.com');
    }

    public function testIsHttpsReturnsTrueForHttpsScheme()
    {
        $request = new Request('GET', 'https://example.com');
        $this->assertTrue($request->isHttps());
    }

    public function testIsAjaxReturnsTrueForAjaxRequest()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

        $request = new Request('GET', 'http://example.com');
        $this->assertTrue($request->isAjax());
    }

    public function testGetClientIpReturnsCorrectIp()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $request = new Request('GET', 'http://example.com');
        $this->assertEquals('127.0.0.1', $request->getClientIp());
    }

    public function testGetMethodReturnsCorrectMethod()
    {
        $request = new Request('GET', 'http://example.com');
        $this->assertSame('GET', $request->getMethod());
    }

    public function testReadFromStream()
    {
        $request = $this->createRequestWithStream(['something' => 'else']);
        $this->assertEquals('{"som', $request->readBody(5));
        $this->assertEquals('eth', $request->readBody(3));
        $this->assertEquals('{"something":"else"}', $request->getBodyContent());
    }

    private function createRequestWithStream(array $post_data)
    {
        $factory = new Psr17Factory();
        $stream = $factory->createStream(json_encode($post_data));
        $request = new Request('POST', 'http://example.com');
        $reflection = new \ReflectionClass($request);
        $property = $reflection->getProperty('request');
        $property->setAccessible(true);
        $psrRequest = $property->getValue($request)->withBody($stream)->withHeader('Content-Type', 'application/json');
        $property->setValue($request, $psrRequest);

        return $request;
    }

}