<?php

namespace Tests\Unit\Dependencies\mockdata;

class FakeClassB
{
    protected FakeClassC $cc;
    private string $created_at;
    private array $data = [];

    public function __construct(FakeClassC $cc)
    {
        $this->created_at = microtime();
        $this->cc = $cc;
    }
    public function exampleMethod(): string
    {
        return "This is a method in FakeClassB.";
    }

    public function getCreationTime()
    {
        return "CLASS B CREATED AT: " . $this->created_at;
    }
    public function getClassC()
    {
        return $this->cc;
    }

    public function putData(mixed $data)
    {
        $this->data[] = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }
}