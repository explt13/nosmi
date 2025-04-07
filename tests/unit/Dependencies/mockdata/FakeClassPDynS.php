<?php

namespace Tests\Unit\Dependencies\mockdata;

class FakeClassPDynS implements IFakeClassPDynS
{
    public function exampleMethod(): string
    {
        return "This is a method in FakeClassPDynS.";
    }
}