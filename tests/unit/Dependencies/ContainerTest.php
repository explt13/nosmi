<?php

namespace Tests\Unit\Dependencies;

use Explt13\Nosmi\Dependencies\Container;
use Explt13\Nosmi\Exceptions\ClassNotFoundException;
use Explt13\Nosmi\Exceptions\DependencyNotSetException;
use Explt13\Nosmi\Interfaces\ContainerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Dependencies\mockdata\FakeClassA;
use Tests\Unit\Dependencies\mockdata\FakeClassB;
use Tests\Unit\Dependencies\mockdata\FakeClassC;
use Tests\Unit\Dependencies\mockdata\FakeClassDepWithoutType;
use Tests\Unit\Dependencies\mockdata\FakeClassFDynNotS;
use Tests\Unit\Dependencies\mockdata\FakeClassRDynS;
use Tests\Unit\Dependencies\mockdata\FakeClassKDynNotS;
use Tests\Unit\Dependencies\mockdata\FakeClassPDynS;
use Tests\Unit\Dependencies\mockdata\FakeClassR;
use Tests\Unit\Dependencies\mockdata\IFakeClassKDynNotS;
use Tests\Unit\Dependencies\mockdata\IFakeClassPDynS;
use Tests\Unit\helpers\Reset;

class ContainerTest extends TestCase
{
    private ContainerInterface|null $container;

    public function setUp(): void
    {
        $this->container = Container::getInstance();
        $this->container->set(FakeClassA::class, FakeClassA::class, true);
        $this->container->set(FakeClassB::class, FakeClassB::class);
        $this->container->set(FakeClassC::class, FakeClassC::class);
    }
    public function tearDown(): void
    {
        Reset::resetSingleton(Container::class);
    }

    public static function depsProvider()
    {
        return [
            "no singleton" => [FakeClassFDynNotS::class, FakeClassFDynNotS::class],
            "singleton" => [FakeClassRDynS::class, FakeClassRDynS::class, true],
            "interface no singleton" => [IFakeClassKDynNotS::class, FakeClassKDynNotS::class, "interface" => true],
            "interface singleton" => [IFakeClassPDynS::class, FakeClassPDynS::class, true, true],
        ];
    }

    public static function invalidDataProvider()
    {
        return [
            "set not existed abstract" => [
                "abstract" => "Tests\Unit\Dependencies\mockdata\FakeClassA",
                "concrete" => FakeClassA::class,
                "fail" => "set not existed abstract"
            ],
            "set not existed class" => [
                "abstract" => FakeClassA::class,
                "concrete" => "Tests\Unit\Dependencies\mockdata\FakeClassA",
                "fail" => "set not existed concrete"
            ],
            "remove not set dep" => [
                "abstract" => FakeClassFDynNotS::class,
                "concrete" => FakeClassFDynNotS::class,
                "fail" => "remove not set dep"
            ],
            "get not set dep" => [
                "abstract" => FakeClassFDynNotS::class,
                "concrete" => FakeClassFDynNotS::class,
                "fail" => "get not set dep"
            ],
            "dep without type" => [
                "abstract" => FakeClassDepWithoutType::class,
                "concrete" => FakeClassDepWithoutType::class,
                "fail" => "autowire dep without type"
            ]
        ];
    }

    #[DataProvider('depsProvider')]
    public function testGetDependency($abstract, $concrete, $singleton = false, $interface = false)
    {
        $this->container->set($abstract, $concrete, $singleton);
        if ($interface) {
            $this->assertInstanceOf($concrete, $this->container->get($abstract));
            $this->assertInstanceOf($abstract, $this->container->get($abstract));
            return;
        }
        $this->assertInstanceOf($abstract, $this->container->get($abstract));
    }

    #[DataProvider('depsProvider')]
    public function testRemoveDependency($abstract, $concrete, $singleton = false, $interface = false)
    {
        $this->container->set($abstract, $concrete, $singleton);
        $this->container->remove($abstract);
        $this->assertFalse($this->container->has($abstract));
    }

    public function testHasDependency()
    {
        $this->assertTrue($this->container->has(FakeClassA::class));
        $this->assertFalse($this->container->has('Tests\Unit\Dependencies\mockdata\NOT_EXISTED_CLASS'));
        $this->container->remove(FakeClassA::class);
        $this->assertFalse($this->container->has(FakeClassA::class));
    }

    public function testSetInterfaceConcreteDependency()
    {
        $this->expectException(ClassNotFoundException::class);
        $this->container->set(IFakeClassPDynS::class, IFakeClassPDynS::class);
    }

    public function testSingleton()
    {
        $class_a0 = $this->container->get(FakeClassA::class);
        $class_a1 = $this->container->get(FakeClassA::class);
        $class_b0 = $this->container->get(FakeClassB::class);
        $class_b1 = $this->container->get(FakeClassB::class);
        $class_c0 = $this->container->get(FakeClassC::class);
        $class_c1 = $this->container->get(FakeClassC::class);

        // singleton object class A (cached)
        $this->assertSame($class_a0, $class_a1);
        $this->assertSame($class_a0->getClassB(), $class_a1->getClassB());
        $this->assertSame($class_a0->getClassB()->getClassC(), $class_a1->getClassB()->getClassC());
        $this->assertSame($class_a0->getClassB()->getCreationTime(), $class_a1->getClassB()->getCreationTime());
        $this->assertSame($class_a0->getClassB()->getClassC()->getCreationTime(), $class_a1->getClassB()->getClassC()->getCreationTime());

        // testing set data to the singleton (should return the same state)
        $class_a0->putData('Alice');
        $class_a1->putData(123);
        $this->assertSame(['Alice', 123], $class_a0->getData());
        $this->assertSame(['Alice', 123], $class_a1->getData());

        // testing set data to the singleton's dependency (should return the same state)
        $class_a0->getClassB()->putData('Kyle');
        $class_a1->getClassB()->putData(188);
        $this->assertSame(['Kyle', 188], $class_a0->getClassB()->getData());
        $this->assertSame(['Kyle', 188], $class_a1->getClassB()->getData());


        // non-cached objects class B and class C (not cached even if are deps of the cached objects, not chained caching)
        $this->assertNotSame($class_b0, $class_b1);
        $this->assertNotSame($class_c0, $class_c1);
        $this->assertNotSame($class_b0->getCreationTime(), $class_b1->getCreationTime());
        $this->assertNotSame($class_b0->getClassC()->getCreationTime(), $class_b1->getClassC()->getCreationTime());
        $this->assertNotSame($class_c0->getCreationTime(), $class_c1->getCreationTime());

        // testing set data to the ordinary instanced class (should NOT return the same state)
        $class_b0->putData('John');
        $class_b1->putData(990);
        $this->assertNotSame(['John', 990], $class_b0->getData());
        $this->assertNotSame(['John', 990], $class_b1->getData());
        $this->assertSame(['John'], $class_b0->getData());
        $this->assertSame([990], $class_b1->getData());

        // put more data into B0 object
        $class_b0->putData(111);
        $this->assertNotSame([990, 111], $class_b1->getData());
        $this->assertSame([990], $class_b1->getData());
    }

    public function testGetSingletonForceNew()
    {
        $class_a0 = $this->container->get(FakeClassA::class);
        $class_a1 = $this->container->get(FakeClassA::class);
        $class_a2 = $this->container->get(FakeClassA::class, true, true);

        // class_a3 should be the same as class_a2
        $class_a3 = $this->container->get(FakeClassA::class);
        $class_a0->putData('Alice');
        $class_a1->putData(123);
        $this->assertSame(['Alice', 123], $class_a0->getData());
        $this->assertSame(['Alice', 123], $class_a1->getData());

        // class_a2 should have a new state, hence getData should be empty
        $this->assertSame([], $class_a2->getData());
        $this->assertSame([], $class_a3->getData());

        // put data to class_a3, classes a2 and a3 should be updated accordingly
        $class_a3->putData('Sam');
        $this->assertSame(['Sam'], $class_a2->getData());
        $this->assertSame(['Sam'], $class_a3->getData());

        // objects that has been get before force reassigning have a deprecated state
        $this->assertSame(['Alice', 123], $class_a0->getData());
    }


    public function testAutowiringDependencies()
    {
        $this->container->set(FakeClassR::class, FakeClassR::class);
        $resolvedInstance = $this->container->get(FakeClassR::class);
        $this->assertInstanceOf(FakeClassR::class, $resolvedInstance);
        $this->assertInstanceOf(FakeClassC::class, $resolvedInstance->getClassC());
    }

    #[DataProvider('invalidDataProvider')]
    public function testExceptions($abstract, $concrete, $fail)
    {
        if ($fail === 'set not existed abstract') {
            $this->expectExceptionWithDetails(
                ClassNotFoundException::class,
                "Class or interface `Tests\Unit\Dependencies\mockdata\NOT_EXISTED_CLASS` is not found.",
                1080
            );
            $this->container->set('Tests\Unit\Dependencies\mockdata\NOT_EXISTED_CLASS', FakeClassA::class);
        }
        if ($fail === 'set not existed concrete') {
            $this->expectExceptionWithDetails(
                ClassNotFoundException::class,
                "Class `Tests\Unit\Dependencies\mockdata\NOT_EXISTED_CLASS` is not found.",
                1080
            );
            $this->container->set(FakeClassA::class, 'Tests\Unit\Dependencies\mockdata\NOT_EXISTED_CLASS');
        }

        if ($fail === 'remove not set dep') {
            $this->expectExceptionWithDetails(
                DependencyNotSetException::class,
                "Cannot unset non-existent dependency $abstract",
                1070
            );
            $this->container->remove($abstract);
        }

        if ($fail === 'get not set dep') {
            $this->expectExceptionWithDetails(
                DependencyNotSetException::class,
                "No binding found for `$abstract`",
                1070
            );
            $this->container->get($abstract);
        }

        if ($fail === 'autowire dep without type') {
            $this->expectExceptionWithDetails(
                \LogicException::class,
                "Unable to resolve argument `class_c` for service `$abstract`",
                1090
            );
            $this->container->set($abstract, $concrete);
            $this->container->get($abstract);
        }
    }

    private function expectExceptionWithDetails(string $exceptionClass, string $message, int $code): void
    {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($message);
        $this->expectExceptionCode($code);
    }
}
