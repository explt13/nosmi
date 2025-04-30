<?php

namespace Tests\Unit\Dependencies;

use Explt13\Nosmi\Dependencies\DependencyManager;
use Explt13\Nosmi\Interfaces\ContainerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;


class DependencyManagerTest extends TestCase
{
    private DependencyManager|null $dep_manager;

    public function setUp(): void
    {
        $this->dep_manager = new DependencyManager();
    }

    public function testLoadDependenciesThrowsFileNotFoundException(): void
    {
        $this->expectException(\Explt13\Nosmi\Exceptions\FileNotFoundException::class);
        $this->dep_manager->loadDependencies('/invalid/path/to/dependencies.php');
    }

    public function testLoadDependenciesThrowsInvalidFileExtensionException(): void
    {
        $this->expectException(\Explt13\Nosmi\Exceptions\InvalidFileExtensionException::class);
        $this->dep_manager->loadDependencies(__DIR__ . '/mockdata/dependencies.txt');
    }

    public function testAddDependencySuccessfully(): void
    {
        $mockContainer = $this->createMock(ContainerInterface::class);
        $mockContainer->expects($this->once())
            ->method('set')
            ->with('TestAbstract', 'TestConcrete', false);

        $this->dep_manager = new DependencyManager();
        $reflection = new \ReflectionClass($this->dep_manager);
        $property = $reflection->getProperty('container');
        $property->setAccessible(true);
        $property->setValue($this->dep_manager, $mockContainer);

        $this->dep_manager->addDependency('TestAbstract', 'TestConcrete');
    }

    public function testHasDependencyReturnsTrue(): void
    {
        $mockContainer = $this->createMock(ContainerInterface::class);
        $mockContainer->expects($this->once())
            ->method('has')
            ->with('TestAbstract')
            ->willReturn(true);

        $this->dep_manager = new DependencyManager();
        $reflection = new \ReflectionClass($this->dep_manager);
        $property = $reflection->getProperty('container');
        $property->setAccessible(true);
        $property->setValue($this->dep_manager, $mockContainer);

        $this->assertTrue($this->dep_manager->hasDependency('TestAbstract'));
    }

    public function testRemoveDependencySuccessfully(): void
    {
        $mockContainer = $this->createMock(ContainerInterface::class);
        $mockContainer->expects($this->once())
            ->method('remove')
            ->with('TestAbstract');

        $this->dep_manager = new DependencyManager();
        $reflection = new \ReflectionClass($this->dep_manager);
        $property = $reflection->getProperty('container');
        $property->setAccessible(true);
        $property->setValue($this->dep_manager, $mockContainer);

        $this->dep_manager->removeDependency('TestAbstract');
    }

    public function testGetDependencySuccessfully(): void
    {
        $mockContainer = $this->createMock(ContainerInterface::class);
        $mockContainer->expects($this->once())
            ->method('get')
            ->with('TestAbstract', false, false)
            ->willReturn(new \stdClass());

        $this->dep_manager = new DependencyManager();
        $reflection = new \ReflectionClass($this->dep_manager);
        $property = $reflection->getProperty('container');
        $property->setAccessible(true);
        $property->setValue($this->dep_manager, $mockContainer);

        $result = $this->dep_manager->getDependency('TestAbstract');
        $this->assertInstanceOf(\stdClass::class, $result);
    }
}