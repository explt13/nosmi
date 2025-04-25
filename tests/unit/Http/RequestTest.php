<?php

namespace Tests\Unit\Http;
use PHPUnit\Framework\TestCase;
use Explt13\Nosmi\Exceptions\NotInArrayException;
use Explt13\Nosmi\Http\HttpFactory;
use Explt13\Nosmi\Http\Request;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Request as Psr7Request;

class RequestTest extends TestCase
{
    private $factory;

    public function setUp(): void
    {
        $this->factory = new Psr17Factory();
    }

    public function testGetParsedBodyParsesJsonBody()
    {
        $request = $this->createRequestWithStream(['key' => 'value']);

        $parsedBody = $request->getParsedBody();
        $this->assertEquals(['key' => 'value'], $parsedBody);
    }

    public function testGetMethodReturnsCorrectMethod()
    {
        $request = new Request(new Psr7Request("GET", 'http://example.com'), $this->factory);
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
        $factory = new HttpFactory(new Psr17Factory());
        $stream = $factory->createStream(json_encode($post_data));
        $request = $factory->createRequest('POST', 'http://example.com');
        $request = $request->withBody($stream)->withHeader('Content-Type', 'application/json');

        return $request;
    }

}