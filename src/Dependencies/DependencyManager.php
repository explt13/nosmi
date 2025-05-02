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
    protected static array $framework_dependencies = [];
    protected static bool $framework_dependencies_loaded = false;

    public function __construct()
    {
        $this->container = Container::getInstance();
    }

    /**
     * @internal
     */
    public function loadFrameworkDependencies(string $path): void
    {
        if (self::$framework_dependencies_loaded) {
            // log
            return;
        }
        self::$framework_dependencies_loaded = true;
        $dependencies = require_once $path;
        $this->setDependencies($dependencies, true);
    }

    /**
     * @param string $path the path to the dependencies
     * @note dependencies structure array<string, string|array{concrete: string, singleton: bool}> 
     */
    public function loadDependencies(string $path): void
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

        $dependencies = require $path;
        $this->setDependencies($dependencies, false);
    }

    public function getDependency(string $abstract, bool $getNew = false, bool $cacheNew = false): object
    {
        return $this->container->get($abstract, $getNew, $cacheNew);
    }

    public function addDependency(string $abstract, string $concrete, bool $singleton = false): void
    {
        if ($this->hasDependency($abstract)) {
            if (in_array($abstract, self::$framework_dependencies)) {
                throw new \RuntimeException("Cannot override $abstract: is framework dependency.");
            }
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

    /**
     * @param array<string, string|array{concrete: string, singleton: bool}> $dependencies
     */
    protected function setDependencies(array $dependencies, bool $framework_deps)
    {
        foreach ($dependencies as $abstract => $dependency)
        {
            if ($framework_deps) {
                self::$framework_dependencies[] = $abstract;
            }

            if (!Types::is_primitive($dependency) && Types::array_is_assoc($dependency)) {
                if (!isset($dependency['concrete'])) {
                    // Log critical
                    throw MissingAssocArrayKeyException::withMessage(sprintf("Cannot set the dependency %s missing the key: %s", $abstract, 'concrete'));
                }
                $this->addDependency($abstract, $dependency['concrete'], $dependency['singleton'] ?? false);
                continue;
            }
            $this->addDependency($abstract, $dependency);
        }
    }
}