<?php
namespace Explt13\Nosmi\Dependencies;

use Explt13\Nosmi\Exceptions\ClassNotFoundException;
use Explt13\Nosmi\Exceptions\DependencyNotSetException;
use Explt13\Nosmi\Interfaces\ContainerInterface;
use Explt13\Nosmi\Interfaces\SingletonInterface;
use Explt13\Nosmi\Traits\SingletonTrait;
use Explt13\Nosmi\Validators\ClassValidator;
use Explt13\Nosmi\Validators\ContainerValidator;

class Container implements ContainerInterface, SingletonInterface
{
    use SingletonTrait;

    protected array $bindings = array();
    protected array $services = array();

    public function set(string $abstract, string $concrete, bool $singleton = false): void
    {
        if (!ClassValidator::isClassOrInterfaceExists($abstract)) {
            throw new ClassNotFoundException($abstract);
        }
        if (!ClassValidator::isClassExists($concrete)) {
            throw ClassNotFoundException::withMessage("Class `$concrete` is not found.");
        }

        $this->bindings[$abstract] = [
            "dependency" => $concrete,
            "singleton" => $singleton,
        ];
    }

    public function remove(string $abstract): void
    {
        if (!ContainerValidator::isDependencyInBindings($this->bindings, $abstract) && 
            !ContainerValidator::isDependencyInServices($this->services, $abstract)) {
            throw DependencyNotSetException::withMessage('Cannot unset non-existent dependency ' . $abstract);
        }
        unset($this->bindings[$abstract]);
        unset($this->services[$abstract]);
    }

    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]);
    }


    public function get(string $abstract, bool $getNew = false, bool $cacheNew = false): object
    {
        if (!$getNew && ContainerValidator::isDependencyInServices($this->services, $abstract)) {
            return $this->services[$abstract];
        }

        if (!ContainerValidator::isDependencyInBindings($this->bindings, $abstract)) {
            throw new DependencyNotSetException($abstract);
        }

        $concrete = $this->autowire($this->bindings[$abstract]["dependency"]);
        if ($this->bindings[$abstract]["singleton"] ?? false) {
            if (!$getNew || ($getNew && $cacheNew)) {
                $this->cacheService($abstract, $concrete);
            }
        }
        return $concrete;
    }

    protected function cacheService(string $abstract, object $concrete)
    {
        $this->services[$abstract] = $concrete;
    }
    
    protected function autowire(string $service): object
    {
        
        $reflectorClass = new \ReflectionClass($service);
        $reflectorConstructor = $reflectorClass->getConstructor();

        if ($reflectorClass->hasMethod('getInstance')) {
            return call_user_func([$service, 'getInstance']);
        }

        if (is_null($reflectorConstructor)) {
            return new $service;
        }
        
        $constructorArgs = $reflectorConstructor->getParameters();
        if (empty($constructorArgs)){
            return new $service;
        }

        $dependencies = [];
        foreach ($constructorArgs as $arg) {
            $argType = $arg->getType();
            if ($argType === null) {
                throw new \LogicException("Unable to resolve argument `{$arg->getName()}` for service `$service`", 1090);
            }
            $dependencies[$arg->getName()] = $this->get($argType->getName());
        }
        return new $service(...$dependencies);
    }
}