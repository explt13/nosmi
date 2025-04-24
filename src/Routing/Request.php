<?php

namespace Explt13\Nosmi\Routing;

use Explt13\Nosmi\Interfaces\IncomingRequestInterface;
use Explt13\Nosmi\Interfaces\LightRequestInterface;
use Explt13\Nosmi\Interfaces\OutgoingRequestInterface;
use Explt13\Nosmi\Traits\ExchangeTrait;
use Explt13\Nosmi\Traits\IncomingRequestTrait;
use Explt13\Nosmi\Traits\OutgoingRequestTrait;
use Explt13\Nosmi\Traits\RequestTrait;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Request class primarily used for outgoing requests
 */
class Request implements LightRequestInterface, IncomingRequestInterface, OutgoingRequestInterface
{
    use ExchangeTrait;
    use RequestTrait;
    use OutgoingRequestTrait;
    use IncomingRequestTrait;

    private RequestInterface $exchange;
    private RequestFactoryInterface&StreamFactoryInterface $factory;

    public function __construct(string $method, string $uri, array $headers = [], string $body = '')
    {
        $this->factory = new Psr17Factory();
        $this->exchange = $this->factory->createRequest($method, $uri);

        // Set headers
        foreach ($headers as $name => $value) {
            $this->exchange = $this->exchange->withHeader($name, $value);
        }

        // Set body
        if (!empty($body)) {
            $stream = $this->factory->createStream($body);
            $this->exchange = $this->exchange->withBody($stream);
        }
    }
}