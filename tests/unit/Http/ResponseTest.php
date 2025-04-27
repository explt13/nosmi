<?php
namespace Tests\Unit\Http;

use Explt13\Nosmi\Http\HttpFactory;
use Explt13\Nosmi\Http\Response;
use Nyholm\Psr7\Response as Psr7Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ResponseTest extends TestCase
{
    public function testWithHeader()
    {
        $response = new Response(new Psr7Response(), new HttpFactory());
        $response = $response->withHeader('X-Test-Header', 'TestValue');

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('TestValue', $response->getHeaderLine('X-Test-Header'));
    }

    public function testWithStatus()
    {
        $response = new Response(new Psr7Response(), new HttpFactory());
        $response = $response->withStatus(404);

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testWithCookie()
    {
        $response = new Response(new Psr7Response(), new HttpFactory());
        $response = $response->withCookieHeader('TestCookie', 'TestValue', ['path' => '/', 'secure' => true]);

        $this->assertStringContainsString('TestCookie=TestValue', $response->getHeaderLine('Set-Cookie'));
        $this->assertStringContainsString('Path=/', $response->getHeaderLine('Set-Cookie'));
        $this->assertStringContainsString('Secure', $response->getHeaderLine('Set-Cookie'));
    }

    public function testWithCors()
    {
        $response = new Response(new Psr7Response(), new HttpFactory());
        $response = $response->withCorsHeader();

        $this->assertEquals('*', $response->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertEquals('GET, POST, PUT, DELETE, OPTIONS', $response->getHeaderLine('Access-Control-Allow-Methods'));
    }

    public function testJson()
    {
        $response = new Response(new Psr7Response(), new HttpFactory());
        $response = $response->withJson(['key' => 'value']);

        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertJsonStringEqualsJsonString('{"key":"value"}', (string) $response->getBody());
    }

    public function testHtml()
    {
        $response = new Response(new Psr7Response(), new HttpFactory());
        $response = $response->withHtml('<h1>Test</h1>');

        $this->assertEquals('text/html; charset=utf-8', $response->getHeaderLine('Content-Type'));
        $this->assertEquals('<h1>Test</h1>', (string) $response->getBody());
    }

    public function testText()
    {
        $response = new Response(new Psr7Response(), new HttpFactory());
        $response = $response->withText('Plain text');

        $this->assertEquals('text/plain; charset=utf-8', $response->getHeaderLine('Content-Type'));
        $this->assertEquals('Plain text', (string) $response->getBody());
    }

    public function testRedirect()
    {
        $response = new Response(new Psr7Response(), new HttpFactory());
        $response = $response->withRedirect('https://example.com');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('https://example.com', $response->getHeaderLine('Location'));
    }

    public function testError()
    {
        $response = new Response(new Psr7Response(), new HttpFactory());
        $response = $response->withError(500, 'Internal Server Error');

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            '{"error":true,"message":"Internal Server Error"}',
            (string) $response->getBody()
        );
    }

    public function testEmpty()
    {
        $response = new Response(new Psr7Response(), new HttpFactory());
        $response = $response->withEmpty();

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEmpty((string) $response->getBody());
    }
}