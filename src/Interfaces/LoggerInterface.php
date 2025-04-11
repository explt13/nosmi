<?php
namespace Explt13\Nosmi\Interfaces;

use Explt13\Nosmi\Logging\LogStatus;

interface LoggerInterface
{
    /**
     * Logs info message
     * @param string $message the message to log
     * @param ?LogFormatterInterface $formatter [optional] set formatter handler for the current log
     * @param string $dest [optional] the destination to the log file, defaults are LOG_FOLDER and LOG_FILE env variables
     * @return void
     */
    public function logInfo(string $message, ?LogFormatterInterface $formatter = null, ?string $dest = null): void;

    /**
     * Logs warning message
     * @param string $message the message to log
     * @param ?LogFormatterInterface $formatter [optional] set formatter handler for the current log
     * @param string $dest [optional] the destination to the log file, defaults are LOG_FOLDER and LOG_FILE env variables
     * @return void
     */
    public function logWarning(string $message, ?LogFormatterInterface $formatter = null, ?string $dest = null): void;

    /**
     * Logs error message
     * @param string $message the message to log
     * @param ?LogFormatterInterface $formatter [optional] set formatter handler for the current log
     * @param string $dest [optional] the destination to the log file, defaults are LOG_FOLDER and LOG_FILE env variables
     * @return void
     */
    public function logError(string $message, ?LogFormatterInterface $formatter = null, ?string $dest = null): void;

    /**
     * Changes the formatter handler for the next and __all subsequent logs__, 
     * the method recommended to be used as __initial set up call__ to set the formmater handler. 
     * For a single log pass a formatter object as a parameter instead. \
     * Priority:
     * 1. Formatter that passed as a parameter to the log* methods
     * 2. Formatter that set for the specific log status
     * 3. Formatter that set for all log statuses
     * @param LogFormatterInterface $formatter formatter handler
     * @param ?LogStatus $forStatus if specified the formatter handler will be set for the selected status
     * @return void
     */
    public function setFormatter(LogFormatterInterface $formatter, ?LogStatus $forStatus = null): void;
}