<?php

namespace Tests\Unit\helpers;

class IncludeFiles
{
    public static function includeUtilFunctions(): void
    {
        require_once dirname(__FILE__, 4) . '/src/Utils/functions.php';
    }
}