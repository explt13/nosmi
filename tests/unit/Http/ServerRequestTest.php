<?php

namespace Tests\Unit\Http;

use Explt13\Nosmi\Exceptions\NotInArrayException;
use Explt13\Nosmi\Http\HttpFactory;
use Explt13\Nosmi\Http\ServerRequest;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest as Psr7ServerRequest;
use PHPUnit\Framework\TestCase;

class ServerRequestTest extends TestCase
{
    private Psr17Factory $factory;

    public function setUp(): void
    {
        $this->factory = new Psr17Factory();
    }

    public function testCreatesRequestFromGlobals()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['SERVER_PORT'] = '7777';
        $_SERVER['REQUEST_URI'] = '/test';

        $request = ServerRequest::capture();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('https://example.com:7777/test', (string) $request->getUri());
    }

    public function testGetHeaderReturnsCorrectHeader()
    {
        $_SERVER['HTTP_CUSTOM_HEADER'] = 'CustomValue';
        $_SERVER['HTTP_CUSTOM_HEADER_2'] = 'CustomValue2';

        $request = new ServerRequest(new Psr7ServerRequest('GET', 'http://example.com', [], null, "1.1", $_SERVER), $this->factory);
        $this->assertEquals(['CustomValue'], $request->getHeader('CUSTOM-HEADER'));
        $this->assertEquals(['CustomValue2'], $request->getHeader('CUSTOM-HEADER-2'));
    }

    public function testReturnsUriWithoutPortIfUriIsDefaultForScheme()
    {
        
        $this->assertEquals('https://something.com/route', (string) (new ServerRequest(new Psr7ServerRequest('GET', 'https://something.com:443/route', [], null, "1.1", $_SERVER), $this->factory))->getUri());
        $this->assertEquals('http://something.com/route', (string) (new ServerRequest(new Psr7ServerRequest('GET', 'http://something.com:80/route', [], null, "1.1", $_SERVER), $this->factory))->getUri());
    }
    public function testGetClientIpReturnsCorrectIp()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $request = new ServerRequest(new Psr7ServerRequest('GET', 'http://example.com', [], null, "1.1", $_SERVER), $this->factory);
        $this->assertEquals('127.0.0.1', $request->getClientIp());
    }
    public function testValidateThrowsExceptionForMissingRequiredField()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('{"name":"name is required."}');

        $request = new ServerRequest(new Psr7ServerRequest('GET', 'http://example.com', [], null, "1.1", $_SERVER), $this->factory);

        $data = $request->getQueryParams();
        $data = $request->validate($data, ['name' => 'required']);
    }

    public function testValidateReturnsDataWhenValid()
    {
        $request = new ServerRequest(new Psr7ServerRequest('GET', 'http://example.com?name=John', [], null, "1.1", $_SERVER), $this->factory);

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

    public function testIsHttpsReturnsTrueForHttpsScheme()
    {
        $request = $request = new ServerRequest(new Psr7ServerRequest('GET', 'https://example.com', [], null, "1.1", $_SERVER), $this->factory);
        $this->assertTrue($request->isHttps());
    }

    public function testIsAjaxReturnsTrueForAjaxRequest()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'xmlhttprequest';

        $request = new ServerRequest(new Psr7ServerRequest('GET', 'https://example.com', [], null, "1.1", $_SERVER), $this->factory);
        
        $this->assertTrue($request->isAjax());
    }


    private function createRequestWithStream(array $post_data)
    {
        $factory = new HttpFactory(new Psr17Factory());
        $stream = $factory->createStream(json_encode($post_data));
        $request = $factory->createServerRequest('POST', 'https://example.com');
        $request = $request->withBody($stream)->withHeader('Content-Type', 'application/json');

        return $request;
    }

}