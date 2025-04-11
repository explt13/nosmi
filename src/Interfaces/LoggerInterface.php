<?php
namespace Explt13\Nosmi\Interfaces;

interface LoggerInterface
{
    /**
     * Log info message
     * @param string $message the message to log
     * @param string $dest [optional] the destination to the log file, defaults are LOG_FOLDER and LOG_FILE env variables
     * @return void
     */
    public function logInfo(string $message, ?string $dest = null): void;
    
    /**
     * Log warning message
     * @param string $message the message to log
     * @param string $dest [optional] the destination to the log file, defaults are LOG_FOLDER and LOG_FILE env variables
     * @return void
     */
    public function logWarning(string $message, ?string $dest = null): void;

    /**
     * Log error message
     * @param string $message the message to log
     * @param string $dest [optional] the destination to the log file, defaults are LOG_FOLDER and LOG_FILE env variables
     * @return void
     */
    public function logError(string $message, ?string $dest = null): void;

    /**
     * Change the formatter class
     * @param LogFormatterInterface $formatter formatter class
     * @return void
     */
    public function changeFormatter(LogFormatterInterface $formatter): void;
}