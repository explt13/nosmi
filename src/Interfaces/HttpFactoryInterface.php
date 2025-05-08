<?php

namespace Explt13\Nosmi\Interfaces;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

interface HttpFactoryInterface extends RequestFactoryInterface,
                                        ResponseFactoryInterface,
                                        UriFactoryInterface,
                                        StreamFactoryInterface,
                                        UploadedFileFactoryInterface,
                                        ServerRequestFactoryInterface
{
    /**
     * Creates and returns an instance of a class implementing the LightClientInterface.
     *
     * @return LightClientInterface An instance of a light client.
     */
    public function createClient(): LightClientInterface;

    /**
     * @return LightRequestInterface&WriteExchangeInterface&ExchangeInterface
     */
    public function createRequest(string $method, $uri): LightRequestInterface;

    /**
     * @return LightResponseInterface&ReadExchangeInterface&WriteExchangeInterface&ExchangeInterface
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): LightResponseInterface;

    /**
     * @return LightServerRequestInterface&ReadExchangeInterface&ExchangeInterface
     */
    public function createServerRequest(?string $method = null, $uri = null, array $serverParams = []): LightServerRequestInterface;
}