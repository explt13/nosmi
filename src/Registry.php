<?php
namespace Explt13\Nosmi;

class Registry
{
    use SingletonTrait;

    private array $properties = [];

    public function setProperty(string $name, mixed $value): void
    {
        $this->properties[$name] = $value;
    }   
    public function getProperty(string $name): mixed
    {
        return $this->properties[$name] ?? null;
    }
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setParams(array $params): void
    {
        if (!empty($params)){
            foreach($params as $k => $v){
                $this->setProperty($k, $v);
            } 
        }
    }
}