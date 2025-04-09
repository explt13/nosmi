<?php
namespace Explt13\Nosmi\Interfaces;

use Explt13\Nosmi\Logging\LogFormatterModes;
use Explt13\Nosmi\Logging\LogStatus;

interface LogInterface
{
    public function log(string $message, string $dest, LogStatus $type, LogFormatterModes $formatMode);
    public function changeFormatter(LogFormatterInterface $formatter);
}