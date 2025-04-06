<?php

namespace Tests\Unit\Dependencies\mockdata;

class FakeClassB
{
    protected FakeClassC $cc;
    public function __construct(FakeClassC $cc)
    {
        $this->cc = $cc;
    }
    public function exampleMethod(): string
    {
        return "This is a method in FakeClassB.";
    }
}