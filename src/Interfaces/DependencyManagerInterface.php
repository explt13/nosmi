<?php
namespace Explt13\Nosmi\Interfaces;

interface DependencyManagerInterface
{
    /**
     * Gets the object from the bindings or services
     * @template T
     * @param class-string<T> $abstract the classname of the interface or class
     * @param bool $getNew [optional] <p>
     * force to get the new instance of the abstract, has an effect only if the abstract has a singleton realization
     * </p>
     * @return T
     */
    public function getDependency(string $abstract, bool $getNew = false): object;
    
    
    public function loadDependencies(array $dependencies): void;
    
    public function addDependency(string $id, string $dependency);
    
    public function removeDependency(string  $id);
}