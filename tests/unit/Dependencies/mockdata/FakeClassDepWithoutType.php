<?php

namespace Tests\Unit\Dependencies\mockdata;

class FakeClassDepWithoutType
{
    private $class_c;
    public function __construct($class_c)
    {
        $this->class_c = $class_c;
    }
}