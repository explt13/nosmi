<?php

namespace Explt13\Nosmi\Http;

use Explt13\Nosmi\Interfaces\IncomingRequestInterface;
use Explt13\Nosmi\Interfaces\LightRequestInterface;
use Explt13\Nosmi\Interfaces\OutgoingRequestInterface;
use Explt13\Nosmi\Traits\ExchangeTrait;
use Explt13\Nosmi\Traits\IncomingRequestTrait;
use Explt13\Nosmi\Traits\OutgoingRequestTrait;
use Explt13\Nosmi\Traits\RequestTrait;
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
    private StreamFactoryInterface $psrFactory;

    public function __construct(RequestInterface $psrRequest, StreamFactoryInterface $psrFactory)
    {
        $this->exchange = $psrRequest;
        $this->psrFactory = $psrFactory;
    }
}