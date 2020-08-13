<?php
declare(strict_types=1);

namespace RCSE\Core\Control;
use RCSE\Core\Utils;

class Log
{
    const EMERGENCY = 'emergency';
    const ALERT = 'alert';
    const CRITICAL = 'critical';
    const ERROR = 'error';
    const WARNING = 'warning';
    const NOTICE = 'notice';
    const INFO = 'info';
    const DEBUG = 'debug';

    private $logFile;
    private $logDir;
    private $fileHandler;
    private $utils;
    private $messageLevels = [
        self::EMERGENCY => 0,
        self::ALERT => 1,
        self::CRITICAL => 2,
        self::ERROR => 3,
        self::WARNING => 4,
        self::NOTICE => 5,
        self::INFO => 6,
        self::DEBUG => 7
    ];
    private $levelThreshold = self::DEBUG;

    public function __construct($levelThreshold = self::DEBUG)
    {
        $this->utils = new Utils();

        $this->setLevelThreshold($levelThreshold);
        $this->setLogfilePath();

        $this->fileHandler = new File($this->logDir, $this->logFile);
        $this->fileHandler->open("c");

    }

    public function __destruct()
    {
        $this->fileHandler->__destruct();
    }

    private function setLogfilePath()
    {
        $datetime = $this->getTimestamp(false)->format('Y-m-d_H-i-s');
        $path = "/logs/{$this->utils->utilsGetClientIP()}/";

        $file = "{$datetime}.log";

        $this->logDir = $path;
        $this->logFile = $file;
    }

    public function setLevelThreshold($levelThreshold)
    {
        $this->levelThreshold = $levelThreshold;
    }

    private function getTimestamp(bool $formatted = true)
    {
        $stamp = date('Y-m-d H:i:s.v');
        $date = new \DateTime($stamp);
        
        if($formatted) {
            return $date->format('Y-m-d H:i:s.v');
        } else {
            return $date;
        }
    }

    public function log($level, string $message, string $source)
    {
        if($this->messageLevels[$this->levelThreshold] <= $this->messageLevels[$level]) {
            return;
        }

        $formattedMessage = $this->formatMessage($level, $message, $source);

        $this->fileHandler->writeLine($formattedMessage);
    }

    private function formatMessage(string $level, string $message, string $source)
    {
        $level = strtoupper($level);
        $formattedMessage = "[{$this->getTimestamp()}][{$level}][{$source}] {$message}".PHP_EOL;
        return $formattedMessage;
    }
}