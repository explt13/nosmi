<?php
namespace Explt13\Nosmi\Interfaces;

use Explt13\Nosmi\Logging\LogFormatterModes;

interface LogFormatterInterface
{
    public function format(array $log, LogFormatterModes $mode): string;
}