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
    private ?LogFormatterInterface $info_formatter = null;
    private ?LogFormatterInterface $warning_formatter = null;
    private ?LogFormatterInterface $error_formatter = null;

    protected function __construct()
    {
        $this->formatter = new DefaultFormatter();
    }

    public function setFormatter(LogFormatterInterface $formatter, ?LogStatus $forStatus = null): void
    {
        if (is_null($forStatus)) {
            $this->formatter = $formatter;
            return;
        }
        switch ($forStatus->name) {
            case "INFO":
                $this->info_formatter = $formatter;
                break;
            case "WARNING":
                $this->warning_formatter = $formatter;
                break;
            case "ERROR":
                $this->error_formatter = $formatter;
                break;
        }
    }

    protected function log(string $message, LogStatus $status, ?LogFormatterInterface $formatter, ?string $dest = null): void
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
        if (is_null($formatter)) {
            $formatter = $this->formatter;
        }
        $logEntry = $formatter->format(['message' => $message, 'status' => $status]);

        if (!file_put_contents($dest, $logEntry, FILE_APPEND | LOCK_EX)) {
            throw new \RuntimeException("Failed to write log file: $dest");
        }
    }

    public function logInfo(string $message, ?LogFormatterInterface $formatter = null, ?string $dest = null): void
    {
        $this->log($message, LogStatus::INFO, $formatter ?? $this->info_formatter, $dest);
    }

    public function logWarning(string $message, ?LogFormatterInterface $formatter = null, ?string $dest = null): void
    {
        $this->log($message, LogStatus::WARNING, $formatter ?? $this->warning_formatter, $dest);
    }

    public function logError(string $message, ?LogFormatterInterface $formatter = null, ?string $dest = null): void
    {
        $this->log($message, LogStatus::ERROR, $formatter ?? $this->error_formatter, $dest);
    }
}