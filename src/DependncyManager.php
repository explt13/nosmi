<?php

namespace Explt13\Nosmi;

use Explt13\Nosmi\interfaces\ContainerInterface;
use Explt13\Nosmi\interfaces\DependencyManagerInterface;

class DependencyManager implements DependencyManagerInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addDependency(string $id, string $dependency, bool $cacheDependecy = false)
    {
        $this->container->set($id, function($container) use ($dependency) {
            $container->autowire($dependency);
        }, $cacheDependecy);
   
    }

    public function removeDependency(string $id)
    {
        $this->container->remove($id);
    }
}