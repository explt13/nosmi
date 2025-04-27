<?php

namespace Explt13\Nosmi\Http;

use Explt13\Nosmi\Interfaces\LightRequestInterface;
use Explt13\Nosmi\Interfaces\WriteExchangeInterface;
use Explt13\Nosmi\Traits\ExchangeTrait;
use Explt13\Nosmi\Traits\RequestTrait;
use Explt13\Nosmi\Traits\WriteExchangeTrait;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Request class that primarily used for outgoing requests
 */
class Request implements LightRequestInterface, WriteExchangeInterface
{
    use ExchangeTrait;
    use RequestTrait;
    use WriteExchangeTrait;

    private RequestInterface $exchange;
    private StreamFactoryInterface $factory;

    public function __construct(RequestInterface $psr_request, StreamFactoryInterface $factory)
    {
        $this->exchange = $psr_request;
        $this->factory = $factory;
    }
}