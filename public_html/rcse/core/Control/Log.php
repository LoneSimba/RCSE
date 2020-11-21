<?php
declare(strict_types=1);

namespace RCSE\Core\Control;
use RCSE\Core\Utils;

class Log
{
    const EMERGENCY = 'Emergency';
    const ALERT = 'Alert';
    const CRITICAL = 'Critical';
    const ERROR = 'Error';
    const WARNING = 'Warning';
    const NOTICE = 'Notice';
    const INFO = 'Info';
    const DEBUG = 'Debug';

    private string $logFile;
    private string $logDir;
    private File $fileHandler;
    private array $messageLevels = [
        self::EMERGENCY => 0,
        self::ALERT => 1,
        self::CRITICAL => 2,
        self::ERROR => 3,
        self::WARNING => 4,
        self::NOTICE => 5,
        self::INFO => 6,
        self::DEBUG => 7
    ];
    private string $levelThreshold = self::DEBUG;

    public function __construct($levelThreshold = self::DEBUG)
    {

        $this->setLevelThreshold($levelThreshold);
        $this->setLogfilePath();

        $this->fileHandler = new File($this->logDir, $this->logFile);
        $this->fileHandler->open("a+");

    }

    public function __destruct()
    {
        $this->fileHandler->__destruct();
    }

    private function setLogfilePath()
    {
        $datetime = Utils::getTimestamp(false)->format('Y-m-d');
        $path = "/logs/". Utils::getClientIP() ."/";

        $file = "{$datetime}.log";

        $this->logDir = $path;
        $this->logFile = $file;
    }

    public function setLevelThreshold($levelThreshold)
    {
        $this->levelThreshold = $levelThreshold;
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
        $formattedMessage = "[". Utils::getTimestamp() ."][{$level}][{$source}] {$message}".PHP_EOL;
        return $formattedMessage;
    }
}