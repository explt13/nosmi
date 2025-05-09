<?php

namespace Tests\Unit\Redis;

use PHPUnit\Framework\TestCase;
use Predis\Client;

class RedisTest extends TestCase
{
    // public function testConnection()
    // {
    //     $r = new Client(array(
    //         'scheme'   => 'tcp',
    //         'host'     => '127.0.0.1',
    //         'port'     => 6379,
    //         'database' => 15
    //     ));

    //     $r->set('user', 112);
    //     $this->assertSame('112', $r->get('user'));
    // }

    public function testGet()
    {
        $r = new Client(array(
            'scheme'   => 'tcp',
            'host'     => '127.0.0.1',
            'port'     => 6379,
            'database' => 15,
            'async'    => true,
            'username' => null,
            'password' => null,
        ));
        $this->assertSame('112', $r->get('user'));
    }
}