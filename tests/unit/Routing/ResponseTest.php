<?php

use PHPUnit\Framework\TestCase;
use Explt13\Nosmi\Routing\Response;
use Psr\Http\Message\ResponseInterface;

class ResponseTest extends TestCase
{
    public function testWithHeader()
    {
        $response = new Response();
        $response->withHeader('X-Test-Header', 'TestValue');

        $this->assertInstanceOf(ResponseInterface::class, $response->get());
        $this->assertEquals('TestValue', $response->get()->getHeaderLine('X-Test-Header'));
    }

    public function testWithStatus()
    {
        $response = new Response();
        $response->withStatus(404);

        $this->assertEquals(404, $response->get()->getStatusCode());
    }

    public function testWithCookie()
    {
        $response = new Response();
        $response->withCookie('TestCookie', 'TestValue', ['path' => '/', 'secure' => true]);

        $this->assertStringContainsString('TestCookie=TestValue', $response->get()->getHeaderLine('Set-Cookie'));
        $this->assertStringContainsString('Path=/', $response->get()->getHeaderLine('Set-Cookie'));
        $this->assertStringContainsString('Secure', $response->get()->getHeaderLine('Set-Cookie'));
    }

    public function testWithCors()
    {
        $response = new Response();
        $response->withCors();

        $this->assertEquals('*', $response->get()->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertEquals('GET, POST, PUT, DELETE, OPTIONS', $response->get()->getHeaderLine('Access-Control-Allow-Methods'));
    }

    public function testJson()
    {
        $response = new Response();
        $response->json(['key' => 'value']);

        $this->assertEquals('application/json', $response->get()->getHeaderLine('Content-Type'));
        $this->assertJsonStringEqualsJsonString('{"key":"value"}', (string) $response->get()->getBody());
    }

    public function testHtml()
    {
        $response = new Response();
        $response->html('<h1>Test</h1>');

        $this->assertEquals('text/html; charset=utf-8', $response->get()->getHeaderLine('Content-Type'));
        $this->assertEquals('<h1>Test</h1>', (string) $response->get()->getBody());
    }

    public function testText()
    {
        $response = new Response();
        $response->text('Plain text');

        $this->assertEquals('text/plain; charset=utf-8', $response->get()->getHeaderLine('Content-Type'));
        $this->assertEquals('Plain text', (string) $response->get()->getBody());
    }

    public function testRedirect()
    {
        $response = new Response();
        $response->redirect('https://example.com');

        $this->assertEquals(302, $response->get()->getStatusCode());
        $this->assertEquals('https://example.com', $response->get()->getHeaderLine('Location'));
    }

    public function testError()
    {
        $response = new Response();
        $response->error(500, 'Internal Server Error');

        $this->assertEquals(500, $response->get()->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            '{"error":true,"message":"Internal Server Error"}',
            (string) $response->get()->getBody()
        );
    }

    public function testEmpty()
    {
        $response = new Response();
        $response->empty();

        $this->assertEquals(204, $response->get()->getStatusCode());
        $this->assertEmpty((string) $response->get()->getBody());
    }
}