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
     * @param bool $cacheNew [optional] <p>
     * __cache__ a new instance of singleton dependency, only __have an effect__ if a __$getNew__ parameter set to __true__
     * </p>
     * @return T
     */
    public function getDependency(string $abstract, bool $getNew = false, bool $cacheNew = false): object;


    /**
     * @internal
     */
    public function loadFrameworkDependencies(string $path): void;
    
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

    /**
     * Adds a dependency to the container.
     *
     * @param string $abstract The abstract type or identifier of the dependency.
     * @param string $concrete The concrete implementation or class name of the dependency.
     * @param bool $singleton Whether the dependency should be treated as a singleton. Defaults to false.
     *
     * @return void
     */
    public function addDependency(string $abstract, string $concrete, bool $singleton = false): void;


    /**
     * Removes a dependency from the container.
     *
     * @param string $abstract The abstract type or identifier of the dependency to remove.
     *
     * @return void
     */
    public function removeDependency(string $abstract);

    /**
     * Checks if a dependency exists in the container.
     *
     * @param string $abstract The abstract type or identifier of the dependency.
     *
     * @return bool True if the dependency exists, false otherwise.
     */
    public function hasDependency(string $abstract);
}