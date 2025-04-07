<?php

namespace Tests\Unit\Dependencies\mockdata;

class FakeClassR
{
    protected FakeClassC $cc;

    public function __construct(FakeClassC $cc)
    {
        $this->cc = $cc;
    }

    public function getClassC()
    {
        return $this->cc;
    }
}
