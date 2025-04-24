<?php

namespace Tests\Unit\Dependencies;

use Explt13\Nosmi\Dependencies\Container;
use Explt13\Nosmi\Dependencies\DependencyManager;
use Explt13\Nosmi\Exceptions\MissingAssocArrayKeyException;
use Explt13\Nosmi\Interfaces\ContainerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Dependencies\mockdata\FakeClassA;
use Tests\Unit\Dependencies\mockdata\FakeClassB;
use Tests\Unit\Dependencies\mockdata\FakeClassC;
use Tests\Unit\Dependencies\mockdata\FakeClassKDynNotS;
use Tests\Unit\Dependencies\mockdata\FakeClassPDynS;
use Tests\Unit\Dependencies\mockdata\IFakeClassKDynNotS;
use Tests\Unit\Dependencies\mockdata\IFakeClassPDynS;
use Tests\Unit\helpers\IncludeFiles;

class DependencyManagerTest extends TestCase
{
    private DependencyManager|null $dep_manager;
    private (MockObject&ContainerInterface)|null $container;

    public static function setUpBeforeClass(): void
    {
        IncludeFiles::includeUtilFunctions();
    }

    public function setUp(): void
    {
        $this->container = $this->createMock(Container::class);
        $this->dep_manager = new DependencyManager($this->container);
    }

    public function tearDown(): void
    {
        $this->container = null;
        $this->dep_manager = null;
    }

    public function testSetDependency()
    {
        $this->container
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo(FakeClassA::class),
                $this->equalTo(FakeClassA::class),
                $this->equalTo(false)
            );

        $this->dep_manager->addDependency(FakeClassA::class, FakeClassA::class);
    }


    public function testLoadDependencies()
    {
        $expectedCalls = [
            [FakeClassA::class, FakeClassA::class, false],
            [IFakeClassPDynS::class, FakeClassPDynS::class, true],
            [IFakeClassKDynNotS::class, FakeClassKDynNotS::class, false],
            [FakeClassB::class, FakeClassB::class, false],
            [FakeClassC::class, FakeClassC::class, false]
        ];

        $invokedCount = $this->exactly(4);
        $this->container
            ->expects($invokedCount)
            ->method('set')
            ->willReturnCallback(function (string $abstract, string $concrete, bool $singleton) use (&$expectedCalls) {
                $expectedCall = array_shift($expectedCalls);
                $this->assertSame($expectedCall[0], $abstract);
                $this->assertSame($expectedCall[1], $concrete);
                $this->assertSame($expectedCall[2], $singleton);
            });
        
        $this->expectException(MissingAssocArrayKeyException::class);
        $this->expectExceptionMessage(sprintf("Cannot set the dependency %s missing the key: concrete", FakeClassC::class));
        $this->expectExceptionCode(1131);
        $this->dep_manager->loadDependencies(__DIR__.'/mockdata/fake_deps.php');
    }
}