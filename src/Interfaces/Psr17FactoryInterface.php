<?php

namespace Explt13\Nosmi\Interfaces;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

interface Psr17FactoryInterface extends RequestFactoryInterface,
                                        ResponseFactoryInterface,
                                        UriFactoryInterface,
                                        StreamFactoryInterface,
                                        UploadedFileFactoryInterface,
                                        ServerRequestFactoryInterface
{
}