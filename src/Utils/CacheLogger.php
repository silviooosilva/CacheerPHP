<?php

namespace Silviooosilva\CacheerPhp\Utils;


/**
 * Class CacheLogger
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class CacheLogger
{
    private mixed $logFile;
    private mixed $maxFileSize; // 5 MB by default
    private string $logLevel;
    private array $logLevels = ['DEBUG', 'INFO', 'WARNING', 'ERROR'];

    public function __construct($logFile = 'cacheer.log', $maxFileSize = 5 * 1024 * 1024, $logLevel = 'DEBUG')
    {
        $this->logFile = $logFile;
        $this->maxFileSize = $maxFileSize;
        $this->logLevel = strtoupper($logLevel);
    }

    /**
    * Logs a info message.
    * 
    * @return void
    */
    public function info($message): void
    {
        $this->log('INFO', $message);
    }

    /**
    * Logs a warning message.
    *
    * @return void
    */
    public function warning($message): void
    {
        $this->log('WARNING', $message);
    }

    /**
    * Logs an error message.
    * 
    * @return void
    */
    public function error($message): void
    {
        $this->log('ERROR', $message);
    }

    /**
    * Logs a debug message.
    * 
    * @return void
    */
    public function debug($message): void
    {
        $this->log('DEBUG', $message);
    }

    /**
     * Checks if the log level is sufficient to log the message.
     *
     * @param mixed $level
     * @return bool
     */
    private function shouldLog(mixed $level): bool
    {
        return array_search($level, $this->logLevels) >= array_search($this->logLevel, $this->logLevels);
    }

    /**
    * Rotates the log file if it exceeds the maximum size.
    * 
    * @return void
    */
    private function rotateLog(): void
    {
        if (file_exists($this->logFile) && filesize($this->logFile) >= $this->maxFileSize) {
            $date = date('Y-m-d_H-i-s');
            rename($this->logFile, "cacheer_$date.log");
        }
    }

    /**
    * Logs a message to the log file.
    * 
    * @param mixed $level
    * @param string $message
    * @return void
    */
    private function log(mixed $level, string $message): void
    {
        if (!$this->shouldLog($level)) {
            return;
        }

        $this->rotateLog();

        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date] [$level] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}
