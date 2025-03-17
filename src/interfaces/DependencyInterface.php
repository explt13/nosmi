<?php
namespace Explt13\Nosmi\interfaces;

interface DependencyInterface
{
    public function addDependency(string $dependency): bool;
    public function removeDependency(string  $dependency): bool;
    public function cacheDependecies(bool $cacheDependencies): bool;
}