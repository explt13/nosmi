<?php

namespace Tests\Unit\Http;

use Explt13\Nosmi\Http\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testSendRequest()
    {
        $client = new Client();
        $client->sendRequest();
    }
}