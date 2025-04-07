<?php

namespace Tests\Unit\Dependencies\mockdata;

class FakeClassC
{
    private string $created_at;
    
    public function __construct()
    {
        $this->created_at = microtime();
    }

    public function exampleMethod(): string
    {
        return "This is a method in FakeClassC.";
    }
    public function getCreationTime()
    {
        return "CLASS C CREATED AT: " . $this->created_at;
    }
}