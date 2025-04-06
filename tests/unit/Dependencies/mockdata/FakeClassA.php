<?php

namespace Tests\Unit\Dependencies\mockdata;

class FakeClassA
{
    protected FakeClassB $cb;
    public function __construct(FakeClassB $cb)
    {
        $this->cb = $cb;
    }
    public function exampleMethod(): string
    {
        return "This is a method in FakeClassA.";
    }
}