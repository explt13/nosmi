<?php

namespace Explt13\Nosmi\Dependencies;

use Explt13\Nosmi\Exceptions\FileNotFoundException;
use Explt13\Nosmi\Exceptions\InvalidFileExtensionException;
use Explt13\Nosmi\Exceptions\MissingAssocArrayKeyException;
use Explt13\Nosmi\Interfaces\ContainerInterface;
use Explt13\Nosmi\Interfaces\DependencyManagerInterface;
use Explt13\Nosmi\Utils\Types;

final class DependencyManager implements DependencyManagerInterface
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getDependency(string $abstract, bool $getNew = false, bool $cacheNew = false): object
    {
        return $this->container->get($abstract, $getNew, $cacheNew);
    }

    /**
     * @param string $path the path to the dependencies
     * @note dependencies structure array<string, string|array{concrete: string, singleton: bool}> 
     */
    public function loadDependencies(?string $path): void
    {
        if (is_null($path)) {
            // LOG
            return;
        }
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }
        if (pathinfo($path, PATHINFO_EXTENSION) !== 'php') {
            throw new InvalidFileExtensionException($path, ['php']);
        }

        $dependencies = require_once $path;
        foreach ($dependencies as $abstract => $dependency)
        {
            if (!Types::is_primitive($dependency) && Types::array_is_assoc($dependency)) {
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
        return $this->container->has($abstract);
    }

    public function removeDependency(string $abstract)
    {
        $this->container->remove($abstract);
    }
}