<?php
declare(strict_types=1);

namespace RCSE\Core\Control;
use Exception;
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

    private File $fileHandler;
    private string $logFile;
    private string $logDir;
    private string $levelThreshold;
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

    public function __construct($levelThreshold = self::DEBUG)
    {
        $this->setLogfilePath();
        $this->levelThreshold = $levelThreshold;
        $this->fileHandler = (new File($this->logDir, $this->logFile))->open("a+");
    }

    /**
     * Writes $message to log file
     *
     * @param string $level Message level, see Log constants
     * @param string $message
     * @param string $source
     * @throws Exception
     */
    public function log(string $level, string $message, string $source) : void
    {
        if (!($this->messageLevels[$this->levelThreshold] <= $this->messageLevels[$level]))
        {
            $formattedMessage = $this->formatMessage($level, $message, $source);

            $this->fileHandler->writeLine($formattedMessage);
        }
    }

    /**
     * Generates logfile path and name
     *
     * @returns void
     * @throws Exception
     */
    private function setLogfilePath() : void
    {
        $datetime = Utils::getTimestamp('Y-m-d');
        $path = "/logs/". Utils::getClientIP() ."/";

        $file = "{$datetime}.log";

        $this->logDir = $path;
        $this->logFile = $file;
    }

    /**
     * Formats given message
     *
     * @param string $level
     * @param string $message
     * @param string $source
     * @return string
     * @throws Exception
     */
    private function formatMessage(string $level, string $message, string $source): string
    {
        $level = strtoupper($level);
        return "[". Utils::getTimestamp() ."][{$level}][{$source}] {$message}".PHP_EOL;
    }
}