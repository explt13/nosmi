<?php

namespace Tests\unit\mockdata\Dependency;

class FakeClassB
{
    private FakeClassC $class_c;
    public int $createdAt;
    public function __construct(FakeClassC $class_c)
    {
        $this->class_c = $class_c;
        $this->createdAt = time();
    }
    public function classBFunction()
    {
        $this->class_c->greet();
    }
}