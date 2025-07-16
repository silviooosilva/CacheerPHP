<?php

namespace Silviooosilva\CacheerPhp\Utils;


/**
 * Class CacheLogger
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class CacheLogger
{
    private $logFile;
    private $maxFileSize; // 5 MB by default
    private $logLevel;
    private $logLevels = ['DEBUG', 'INFO', 'WARNING', 'ERROR'];

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
    public function info($message)
    {
        $this->log('INFO', $message);
    }

    /**
    * Logs a warning message.
    *
    * @return void
    */
    public function warning($message)
    {
        $this->log('WARNING', $message);
    }

    /**
    * Logs an error message.
    * 
    * @return void
    */
    public function error($message)
    {
        $this->log('ERROR', $message);
    }

    /**
    * Logs a debug message.
    * 
    * @return void
    */
    public function debug($message)
    {
        $this->log('DEBUG', $message);
    }

    /**
    * Checks if the log level is sufficient to log the message.
    *
    * @param mixed $level
    * @return string|int|false
    */
    private function shouldLog(mixed $level)
    {
        return array_search($level, $this->logLevels) >= array_search($this->logLevel, $this->logLevels);
    }

    /**
    * Rotates the log file if it exceeds the maximum size.
    * 
    * @return void
    */
    private function rotateLog()
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
    private function log($level, $message)
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
