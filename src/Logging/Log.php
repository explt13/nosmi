<?php

namespace Explt13\Nosmi\Logging;

use Explt13\Nosmi\Interfaces\LogFormatterInterface;
use Explt13\Nosmi\Interfaces\LogInterface;

class Log implements LogInterface
{
    private LogFormatterInterface $formatter; 

    public function __construct()
    {
        $this->formatter = new LogFormatter();
    }

    /**
     * @param LogFormatterInterface $formatter - sets formatter to use to format a log
     */
    public function changeFormatter(LogFormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    public function log(string $message, string $dest, LogStatus $status = LogStatus::INFO, LogFormatterModes $formatMode = LogFormatterModes::Brief)
    {
        $dir = dirname($dest);
        if (!file_exists($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException("Failed to create log directory: $dir");
        }

        $logEntry = $this->formatter->format(['message' => $message, 'status' => $status], $formatMode);

        if (!@file_put_contents($dest, $logEntry, FILE_APPEND | LOCK_EX)) {
            throw new \RuntimeException("Failed to write log file: $dest");
        }
    }
}