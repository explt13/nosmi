<?php

namespace Tests\unit;

use Explt13\Nosmi\Container;
use PHPUnit\Framework\TestCase;

class DependecyTest extends TestCase
{
    public function testContainerCache()
    {
        $container = Container::getInstance();
        $fake_class_a0_no_cache = $container->get(FakeClassA::class, false);
        $fake_class_a0_cache = $container->get(FakeClassA::class, true);
        
        $fake_class_a0_no_cache->callMe();
        $this->expectOutputString('Hello World!');
        
        sleep(2);   
        
        $fake_class_a1_no_cache = $container->get(FakeClassA::class, false);
        $fake_class_b1_no_cache = $container->get(FakeClassB::class, false);
        $fake_class_b1_cache = $container->get(FakeClassB::class, true);
        $fake_class_a1_cache = $container->get(FakeClassA::class, true);

        $this->assertGreaterThan($fake_class_a0_no_cache->createdAt, $fake_class_a1_no_cache->createdAt);
        $this->assertGreaterThan($fake_class_a0_no_cache->createdAt, $fake_class_b1_no_cache->createdAt);
        $this->assertEquals($fake_class_a1_no_cache->createdAt, $fake_class_b1_no_cache->createdAt);

        $this->assertEquals($fake_class_a0_cache->createdAt, $fake_class_a1_cache->createdAt);
        $this->assertEquals($fake_class_a0_cache->createdAt, $fake_class_a1_cache->createdAt);
        $this->assertEquals($fake_class_a0_cache->createdAt, $fake_class_b1_cache->createdAt);
        $this->assertEquals($fake_class_a1_cache->createdAt, $fake_class_b1_cache->createdAt);
    }
}