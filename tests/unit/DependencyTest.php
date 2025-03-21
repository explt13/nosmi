<?php

namespace Tests\unit;

use Explt13\Nosmi\Container;
use Explt13\Nosmi\DependencyManager;
use PHPUnit\Framework\TestCase;
use Tests\unit\mockdata\Dependency\FakeClassA;
use Tests\unit\mockdata\Dependency\FakeClassB;

class DependencyTest extends TestCase
{
    public function testContainerCache()
    {
        $container = Container::getInstance();
        // $dependencyManager = new DependencyManager($container);
        
        // $dependencyManager->addDependency();

        $fake_class_a_first_no_cache = $container->get(FakeClassA::class, false);
        $fake_class_a_first_cache = $container->get(FakeClassA::class, true);
        $fake_class_r_first_no_cache = $container->get(FakeClassB::class, false);
        $fake_class_r_first_cache = $container->get(FakeClassB::class, true);
        
        $fake_class_a_first_no_cache->callMe();
        $this->expectOutputString('Hello World!');
        
        sleep(2);   
        
        $fake_class_a_second_no_cache = $container->get(FakeClassA::class, false);
        $fake_class_b_second_no_cache = $container->get(FakeClassB::class, false);
        $fake_class_a_second_cache = $container->get(FakeClassA::class, true);
        $fake_class_b_second_cache = $container->get(FakeClassB::class, true);

        // no-cached classes that were created after a while SHOULD NOT HAVE the same creation time 
        $this->assertGreaterThan($fake_class_a_first_no_cache->createdAt, $fake_class_a_second_no_cache->createdAt);
        $this->assertGreaterThan($fake_class_a_first_no_cache->createdAt, $fake_class_b_second_no_cache->createdAt);
        
        // no-cached classes that were created simultaneously SHOULD HAVE the same creation time  
        $this->assertEquals($fake_class_a_second_no_cache->createdAt, $fake_class_b_second_no_cache->createdAt);
        $this->assertEquals($fake_class_a_second_no_cache->createdAt, $fake_class_a_second_cache->createdAt);
        
        // cached classes that were created after a while SHOULD HAVE the same creation time
        $this->assertEquals($fake_class_a_first_cache->createdAt, $fake_class_a_second_cache->createdAt);
        // cached class that has dependency
        $this->assertEquals($fake_class_a_first_cache->createdAt, $fake_class_b_second_cache->createdAt);

        $this->assertEquals($fake_class_a_second_cache->createdAt, $fake_class_b_second_cache->createdAt);
    }
}