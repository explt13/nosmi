<?php

namespace Tests\Unit\Http;

use Buzz\Browser;
use Explt13\Nosmi\Http\Client;
use Explt13\Nosmi\Http\Response;
use Explt13\Nosmi\Interfaces\HttpFactoryInterface;
use Nyholm\Psr7\Response as Psr7Response;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private $mockBrowser;
    private $mockClient;
    private $mockHttpFactory;

    public function setUp(): void
    {
        $this->mockBrowser = $this->createMock(Browser::class);
        $this->mockBrowser->method('get')->willReturn(new Psr7Response());
        $this->mockBrowser->method('post')->willReturn(new Psr7Response());
        $this->mockBrowser->method('put')->willReturn(new Psr7Response());
        $this->mockBrowser->method('delete')->willReturn(new Psr7Response());



        $this->mockHttpFactory = $this->createMock(HttpFactoryInterface::class);

        $this->mockClient = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([$this->mockHttpFactory])
            ->onlyMethods(['createBrowser'])
            ->getMock();
        $this->mockClient->method('createBrowser')->willReturn($this->mockBrowser);
    }
    public function testGet()
    {
        $response = $this->mockClient->get('http://example.com');
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testPost()
    {
        $response = $this->mockClient->post('http://example.com', [], 'body');
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testPut()
    {
        $response = $this->mockClient->put('http://example.com', [], 'body');
        $this->assertInstanceOf(Response::class, $response);
    }

    public function testDelete()
    {
        $response = $this->mockClient->delete('http://example.com');
        $this->assertInstanceOf(Response::class, $response);
    }
}