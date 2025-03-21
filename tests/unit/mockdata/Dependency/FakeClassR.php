<?php

namespace Tests\unit\mockdata\Dependency;

class FakeClassR
{
    private FakeClassB $class_b;
    public function __cosntruct(FakeClassB $class_b)
    {
        $this->class_b = $class_b;
    }

    public function checkWhenClassBWasCreated()
    {
        return $this->class_b->createdAt;
    }
}
