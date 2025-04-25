<?php

namespace Explt13\Nosmi\Http;
use Buzz\Browser;
use Buzz\Client\FileGetContents;
use Explt13\Nosmi\Interfaces\Psr17FactoryInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

class Client
{
    protected Psr17FactoryInterface $factory; 
    function sendRequest()
    {
        $this->factory = new HttpFactory(new Psr17Factory());
        $client = new FileGetContents($this->factory);
        $browser = new Browser($client, $this->factory);
        $response = $browser->get('https://www.google.com');

    
        $request = $browser->getLastRequest();
        echo $request->getUri();
        // $response is a PSR-7 object.
        echo $response->getStatusCode();
    }
}