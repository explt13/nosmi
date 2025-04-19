<?php

namespace Explt13\Nosmi\Interfaces;

interface ContainerInterface
{
    /**
     * Sets the dependency to the bindings
     * @param string $abstract the classname of the interface or class
     * @param string $concrete the classname of the class
     * @param bool $singleton [optional] <p> 
     * set to true to cache the dependency
     * </p>
     * @return void
     * @throws ClassNotFoundException if the class and interface with the $abstract name does not exist
     */
    public function set(string $abstract, string $concrete, bool $singleton = false): void;
    
    /**
     * Checks whether the abstract is present in the bindings
     * @param string $abstract the classname of the interface or class
     * @return bool true if the abstract presents in the bindings and is NOT null, otherwise false
     */
    public function has(string $abstract): bool;

    /**
     * Gets the concrete class instance from the bindings or services
     * @template T
     * @param class-string<T> $abstract the classname of the interface or class
     * @param bool $getNew [optional] <p>
     * force to get the new instance of the singleton abstract, __does NOT cache__ a new abstract by default
     * </p>
     * @param bool $cacheNew [optional] <p>
     * __cache__ a new instance of singleton dependency, only __have an effect__ if a __$getNew__ parameter set to __true__
     * </p>
     * @return T
     * @throws DependencyNotSetException if the $abstract is not set
     */
    public function get(string $abstract, bool $getNew = false, bool $cacheNew = false): object;

    /**
     * Removes abstract from the bindings and services
     * @param string $abstract the classname of the abstract or interface
     * @return void
     * @throws DependencyNotSetException if the $abstract is not set
     */
    public function remove(string $abstract): void;
}