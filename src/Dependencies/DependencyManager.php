<?php

namespace Explt13\Nosmi;

use Explt13\Nosmi\Interfaces\ContainerInterface;
use Explt13\Nosmi\Interfaces\DependencyManagerInterface;

final class DependencyManager implements DependencyManagerInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getDependency(string $abstract, bool $getNew = false): object
    {
        return $this->container->get($abstract, $getNew);
    }

    public function addDependency(string $abstract, string $concrete, bool $singleton = false)
    {
        $this->container->set($abstract, $concrete, $singleton);
    }

    public function hasDependency(string $abstract)
    {
        $this->container->has($abstract);
    }

    public function removeDependency(string $abstract)
    {
        $this->container->remove($abstract);
    }
}