<?php

namespace Explt13\Nosmi;

use Explt13\Nosmi\interfaces\ContainerInterface;
use Explt13\Nosmi\interfaces\DependencyInterface;

class Dependency implements DependencyInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addDependency(string $dependency, bool $cacheDependecy = true): bool
    {
        $this->container->set($dependency, function($container) use ($dependency) {
            $container->autowire($dependency);
        }, $cacheDependecy);
        return true;
    }

    public function removeDependency(string $dependency): bool
    {
        $removed = $this->container->remove($dependency);
        if ($removed) {
            return true;
        }
        return false;
    }
}