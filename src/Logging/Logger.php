<?php

namespace Explt13\Nosmi\Logging;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Interfaces\LogFormatterInterface;
use Explt13\Nosmi\Interfaces\LoggerInterface;
use Explt13\Nosmi\Traits\SingletonTrait;

class Logger implements LoggerInterface
{
    use SingletonTrait;

    private LogFormatterInterface $formatter; 

    public function __construct()
    {
        $this->formatter = new DefaultFormatter();
    }

    public function changeFormatter(LogFormatterInterface $formatter): void
    {
        $this->formatter = $formatter;
    }

    protected function log(string $message, LogStatus $status, ?string $dest = null): void
    {
        $config = AppConfig::getInstance();
        if (is_null($dest)) {
            $log_dir = $config->get('LOG_FOLDER');
            $log_file = $config->get("LOG_FILE_$status->name");

            if (!($log_dir && $log_file)) {
                throw new \LogicException("LOG_FOLDER and/or LOG_FILE env variables are not set, provide a valid value or specify `dest` parameter");
            }
            $dest = $log_dir . '/' . $log_file;
        } else {
            $log_dir = dirname($dest);
        }

        if (!file_exists($log_dir) && !mkdir($log_dir, 0755, true) && !is_dir($log_dir)) {
            throw new \RuntimeException("Failed to create log directory: $log_dir");
        }
        $logEntry = $this->formatter->format(['message' => $message, 'status' => $status]);

        if (!file_put_contents($dest, $logEntry, FILE_APPEND | LOCK_EX)) {
            throw new \RuntimeException("Failed to write log file: $dest");
        }
        
    }

    public function logInfo(string $message, ?string $dest = null): void
    {
        $this->log($message, LogStatus::INFO, $dest);
    }

    public function logWarning(string $message, ?string $dest = null): void
    {
        $this->log($message, LogStatus::WARNING, $dest);
    }

    public function logError(string $message, ?string $dest = null): void
    {
        $this->log($message, LogStatus::ERROR, $dest);
    }
}