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
use Tests\Unit\Dependencies\mockdata\IFakeClassKDynNotS;
use Tests\Unit\Dependencies\mockdata\IFakeClassPDynS;
use Tests\Unit\helpers\SingletonReset;

class ContainerTest extends TestCase
{
    private ContainerInterface|null $container;

    public function setUp(): void
    {
        $this->container = Container::getInstance();
        $this->container->set(FakeClassA::class, FakeClassA::class);
        $this->container->set(FakeClassB::class, FakeClassB::class);
        $this->container->set(FakeClassC::class, FakeClassC::class);
    }
    public function tearDown(): void
    {
        SingletonReset::reset(Container::class);
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
    public function testGet($abstract, $concrete, $singleton = false, $interface = false)
    {
        $this->container->set($abstract, $concrete, $singleton);
        if ($interface) {
            $this->assertEquals(new $concrete, $this->container->get($abstract));
            return;
        }
        $this->assertEquals(new $abstract, $this->container->get($abstract));
    }

    #[DataProvider('depsProvider')]
    public function testRemove($abstract, $concrete, $singleton = false, $interface = false)
    {
        $this->container->set($abstract, $concrete, $singleton);
        $this->container->remove($abstract);
        $this->assertFalse($this->container->has($abstract));        
    }

    public function testHas()
    {
        $this->assertTrue($this->container->has(FakeClassA::class));
        $this->assertFalse($this->container->has('Tests\Unit\Dependencies\mockdata\NOT_EXISTED_CLASS'));
    }

    #[DataProvider('invalidDataProvider')]
    public function testExceptions($abstract, $concrete, $fail)
    {
        if ($fail === 'set not existed abstract') {
            $this->expectException(ClassNotFoundException::class);
            $this->expectExceptionMessage("Class or interface `Tests\Unit\Dependencies\mockdata\NOT_EXISTED_CLASS` not found.");
            $this->expectExceptionCode(1080);
            $this->container->set('Tests\Unit\Dependencies\mockdata\NOT_EXISTED_CLASS', FakeClassA::class);
        }
        if ($fail === 'set not existed concrete') {
            $this->expectException(ClassNotFoundException::class);
            $this->expectExceptionMessage("Class or interface `Tests\Unit\Dependencies\mockdata\NOT_EXISTED_CLASS` not found.");
            $this->expectExceptionCode(1080);
            $this->container->set(FakeClassA::class, 'Tests\Unit\Dependencies\mockdata\NOT_EXISTED_CLASS');
        }

        if ($fail === 'remove not set dep') {
            $this->expectException(DependencyNotSetException::class);
            $this->expectExceptionMessage("Cannot unset non-existent dependency $abstract");
            $this->expectExceptionCode(1070);
            $this->container->remove($abstract);
        }

        if ($fail === 'get not set dep') {
            $this->expectException(DependencyNotSetException::class);
            $this->expectExceptionMessage("No binding found for `$abstract`");
            $this->expectExceptionCode(1070);
            $this->container->get($abstract);
        }

        if ($fail === 'autowire dep without type') {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage("Unable to resolve argument `class_c` for service `$abstract`");
            $this->expectExceptionCode(1090);
            $this->container->set($abstract, $concrete);
            $this->container->get($abstract);
        }
       
    }
}