<?php

namespace Silviooosilva\CacheerPhp\Utils;

class CacheLogger
{
    private $logFile;
    private $maxFileSize; // Tamanho máximo do arquivo em bytes (5MB)
    private $logLevel;
    private $logLevels = ['DEBUG', 'INFO', 'WARNING', 'ERROR'];

    public function __construct($logFile = 'cacheer.log', $maxFileSize = 5 * 1024 * 1024, $logLevel = 'DEBUG')
    {
        $this->logFile = $logFile;
        $this->maxFileSize = $maxFileSize;
        $this->logLevel = strtoupper($logLevel);
    }

    private function shouldLog($level)
    {
        return array_search($level, $this->logLevels) >= array_search($this->logLevel, $this->logLevels);
    }

    private function rotateLog()
    {
        if (file_exists($this->logFile) && filesize($this->logFile) >= $this->maxFileSize) {
            $date = date('Y-m-d_H-i-s');
            rename($this->logFile, "cacheer_$date.log");
        }
    }

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

    public function info($message)
    {
        $this->log('INFO', $message);
    }

    public function warning($message)
    {
        $this->log('WARNING', $message);
    }

    public function error($message)
    {
        $this->log('ERROR', $message);
    }

    public function debug($message)
    {
        $this->log('DEBUG', $message);
    }
}
