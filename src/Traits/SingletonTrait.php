<?php
namespace Explt13\Nosmi\Traits;

trait SingletonTrait
{
    private static $instance;
    
    private function __construct() {}
    final protected function __clone() {}
    final public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }
    
    /**
     * @return static
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}