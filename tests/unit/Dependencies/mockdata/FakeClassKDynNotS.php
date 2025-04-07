<?php

namespace Tests\Unit\Dependencies\mockdata;

class FakeClassKDynNotS implements IFakeClassKDynNotS
{
    public function exampleMethod(): string
    {
        return "This is a method in FakeClassKDynNotS.";
    }
}