<?php

namespace Tests\unit\mockdata\Dependency;

class FakeClassA
{
    private FakeClassB $class_b;
    public int $createdAt;
    public function __construct(FakeClassB $class_b)
    {
        $this->class_b = $class_b;
        $this->createdAt = time();
    }
    public function callMe()
    {
        $this->class_b->classBFunction();
    }
}