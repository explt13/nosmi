<?php
namespace Explt13\Nosmi\Logging;

interface LogInterface
{
    public function log(string $message, string $dest, LogStatus $type, LogFormatterModes $formatMode);
    public function changeFormatter(LogFormatterInterface $formatter);
}