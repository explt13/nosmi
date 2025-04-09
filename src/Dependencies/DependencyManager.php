<?php

namespace Explt13\Nosmi\Dependencies;

use Explt13\Nosmi\Exceptions\MissingAssocArrayKeyException;
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

    /**
     * @param array<string, string|array{concrete: string, singleton: bool}> $dependencies
     */
    public function loadDependencies(array $dependencies): void
    {
        foreach ($dependencies as $abstract => $dependency)
        {
            if (!is_primitive($dependency) && array_is_assoc($dependency)) {
                if (!isset($dependency['concrete'])) {
                    // Log critical
                    throw MissingAssocArrayKeyException::withMessage(sprintf("Cannot set the dependency %s missing the key: %s", $abstract, 'concrete'));
                }
                $this->container->set($abstract, $dependency['concrete'], $dependency['singleton'] ?? false);
                continue;
            }
            $this->container->set($abstract, $dependency);
        }
    }

    public function addDependency(string $abstract, string $concrete, bool $singleton = false): void
    {
        if ($this->hasDependency($abstract)) {
            // log warning
        }
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