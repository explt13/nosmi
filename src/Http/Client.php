<?php

namespace Explt13\Nosmi\Http;
use Buzz\Browser;
use Buzz\Client\FileGetContents;
use Explt13\Nosmi\Interfaces\HttpFactoryInterface;

class Client
{
    protected HttpFactoryInterface $factory;
    public function __construct(HttpFactoryInterface $factory)
    {
        $this->factory = $factory;
    }
    
    public function get(string $url, array $headers = []): Response
    {
        $browser = $this->createBrowser();
        $response = $browser->get($url, $headers);
        return new Response($response, $this->factory);
    }

    public function post(string $url, array $headers = [], string $body = ''): Response
    {
        $browser = $this->createBrowser();
        $response = $browser->post($url, $headers, $body);
        return new Response($response, $this->factory);
    }

    public function put(string $url, array $headers = [], string $body = ''): Response
    {
        $browser = $this->createBrowser();
        $response = $browser->put($url, $headers, $body);
        return new Response($response, $this->factory);
    }

    public function delete(string $url, array $headers = []): Response
    {
        $browser = $this->createBrowser();
        $response = $browser->delete($url, $headers);
        return new Response($response, $this->factory);
    }

    protected function createBrowser(): Browser
    {
        $client = new FileGetContents($this->factory);
        return new Browser($client, $this->factory);
    }
}