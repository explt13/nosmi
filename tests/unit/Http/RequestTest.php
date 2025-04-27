<?php

namespace Tests\Unit\Http;
use PHPUnit\Framework\TestCase;
use Explt13\Nosmi\Http\HttpFactory;
use Explt13\Nosmi\Http\Request;
use Nyholm\Psr7\Request as Psr7Request;

class RequestTest extends TestCase
{
    public function testGetMethod()
    {
        $psrRequest = new Psr7Request('GET', 'https://example.com');
        $factory = $this->createMock(\Psr\Http\Message\StreamFactoryInterface::class);

        $request = new Request($psrRequest, $factory);

        $this->assertSame('GET', $request->getMethod());
    }

    public function testGetUri()
    {
        $psrRequest = new Psr7Request('GET', 'https://example.com');
        $factory = $this->createMock(\Psr\Http\Message\StreamFactoryInterface::class);

        $request = new Request($psrRequest, $factory);

        $this->assertSame('https://example.com', (string) $request->getUri());
    }

    public function testWithMethod()
    {
        $psrRequest = new Psr7Request('GET', 'https://example.com');
        $factory = $this->createMock(\Psr\Http\Message\StreamFactoryInterface::class);

        $request = new Request($psrRequest, $factory);
        $newRequest = $request->withMethod('POST');

        $this->assertNotSame($request, $newRequest);
        $this->assertSame('POST', $newRequest->getMethod());
    }

    public function testWithUri()
    {
        $psrRequest = new Psr7Request('GET', 'https://example.com');
        $factory = $this->createMock(\Psr\Http\Message\StreamFactoryInterface::class);

        $request = new Request($psrRequest, $factory);
        $newUri = new \Nyholm\Psr7\Uri('https://new-example.com');
        $newRequest = $request->withUri($newUri);

        $this->assertNotSame($request, $newRequest);
        $this->assertSame('https://new-example.com', (string) $newRequest->getUri());
    }
}