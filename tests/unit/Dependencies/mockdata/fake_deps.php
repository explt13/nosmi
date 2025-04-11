<?php

use Tests\Unit\Dependencies\mockdata\FakeClassA;
use Tests\Unit\Dependencies\mockdata\FakeClassB;
use Tests\Unit\Dependencies\mockdata\FakeClassC;
use Tests\Unit\Dependencies\mockdata\FakeClassKDynNotS;
use Tests\Unit\Dependencies\mockdata\FakeClassPDynS;
use Tests\Unit\Dependencies\mockdata\IFakeClassKDynNotS;
use Tests\Unit\Dependencies\mockdata\IFakeClassPDynS;

return [
    FakeClassA::class => [
        "concrete" => FakeClassA::class,
        "singleton" => false
    ],
    IFakeClassPDynS::class => [
        "concrete" => FakeClassPDynS::class,
        "singleton" => true
    ],
    IFakeClassKDynNotS::class => [
        "concrete" => FakeClassKDynNotS::class,
        "singleton" => false
    ],
    FakeClassB::class => FakeClassB::class,
    
    // should fail because of missing concrete key
    FakeClassC::class => [
        "concrete2" => FakeClassC::class,
    ]
];
