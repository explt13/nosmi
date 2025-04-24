<?php
namespace Explt13\Nosmi\Interfaces;

use Explt13\Nosmi\Logging\LogFormatterModes;

interface LogFormatterInterface
{
    /**
     * Format a log entry
     * @param array $log a log entry
     * @return string a formatted log string that is ready to be logged
     */
    public function format(array $log): string;
}