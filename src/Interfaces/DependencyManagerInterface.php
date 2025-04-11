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
    
    /**
     * Load an array of dependencies
     * @param string $path the path to the dependencies file
     * @example dependencies structure: \
     * [ \
     *   "InterfaceA" => "ClassA", \
     *   "InterfaceB" => [ \
     *       "concrete" => "ClassB",
     *       "singleton" => true \
     *   ] \
     * ]
     */
    public function loadDependencies(string $path): void;
    
    public function addDependency(string $abstract, string $concrete, bool $singleton = false): void;
    
    public function removeDependency(string $abstract);

    public function hasDependency(string $abstract);
}