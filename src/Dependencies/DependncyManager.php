<?php

namespace Explt13\Nosmi;

use Explt13\Nosmi\Interfaces\ContainerInterface;
use Explt13\Nosmi\Interfaces\DependencyManagerInterface;

class DependencyManager implements DependencyManagerInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addDependency(string $abstract, string $dependency, bool $singleton = false)
    {
        $this->container->set($abstract, function($container) use ($dependency) {
            $container->autowire($dependency);
        }, $singleton);

        $this->container->set($abstract, $dependency, $singleton);
   
    }

    public function removeDependency(string $id)
    {
        $this->container->remove($id);
    }
}