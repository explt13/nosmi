<?php
namespace Explt13\Nosmi\Interfaces;

interface DependencyManagerInterface
{
    public function addDependency(string $id, string $dependency);
    public function removeDependency(string  $id);
}