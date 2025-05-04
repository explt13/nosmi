<?php

namespace Explt13\Nosmi\Logging;

use Explt13\Nosmi\AppConfig\AppConfig;
use Explt13\Nosmi\Interfaces\LogFormatterInterface;
use Explt13\Nosmi\Interfaces\LoggerInterface;
use Explt13\Nosmi\Traits\SingletonTrait;

class Logger implements LoggerInterface
{
    use SingletonTrait;

    protected LogFormatterInterface $formatter;
    protected ?LogFormatterInterface $info_formatter = null;
    protected ?LogFormatterInterface $warning_formatter = null;
    protected ?LogFormatterInterface $error_formatter = null;
    protected bool $log_enabled;

    protected function __construct()
    {
        $this->formatter = new DefaultFormatter();
        $this->log_enabled = AppConfig::getInstance()->get('LOG_ON') ?? false;
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
        if (!$this->log_enabled) {
            return;
        }

        $config = AppConfig::getInstance();
        if (is_null($dest)) {
            $log_dir = $config->get('LOG');
            if (!$config->get("LOG_{$status->name}_FILE")) {
                $log_file = $config->get("LOG_FILE");
            } else {
                $log_file = $config->get("LOG_{$status->name}_FILE");
            }

            if (!($log_dir && $log_file)) {
                throw new \LogicException("LOG and/or LOG_(TYPE)?_FILE env variables are not set, provide a valid value or specify `dest` parameter");
            }
            $dest = $log_file;
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