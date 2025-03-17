<?php
namespace Explt13\Nosmi\Logging;

interface LogFormatterInterface
{
    public function format(array $log, LogFormatterModes $mode): string;
}