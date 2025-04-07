<?php

namespace Tests\Unit\Dependencies\mockdata;

class FakeClassA
{
    protected FakeClassB $cb;
    private array $data = [];

    public function __construct(FakeClassB $cb)
    {
        $this->cb = $cb;
    }
    public function exampleMethod(): string
    {
        return "This is a method in FakeClassA.";
    }

    public function putData(mixed $data)
    {
        $this->data[] = $data;
    }
    public function getData(): array
    {
        return $this->data;
    }

    public function getClassB()
    {
        return $this->cb;
    }
}
