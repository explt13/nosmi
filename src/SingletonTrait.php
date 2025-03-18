<?php
namespace Explt13\Nosmi;

trait SingletonTrait{
    private static $instance;
    
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    
    /**
     * @return static
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}