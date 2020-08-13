<?php
declare(strict_types=1);

namespace RCSE\Core\Control;

use RCSE\Core\File as CoreFile;

/** File Handler, provides functions to write and read files */
class File
{
    private $fileStream;
    private $fileName;
    private $fileDir;
    private $rootDir;
    private $filePerms = 0777;

    /**
     * If you intend to use writeLine, you'll have to set $fileDir and $fileName here
     *
     * @param string $fileDir File directory w\o ROOT directory
     * @param string $fileName Filename
     */
    public function __construct(string $fileDir, string $fileName)
    {
        $this->rootDir = $_SERVER['DOCUMENT_ROOT']; 
        $this->fileDir = $this->rootDir . $fileDir;
        $this->fileName = $fileName;
    }

    public function __destruct()
    {
        if ($this->fileSteram) {
            $this->close();
        }
    }

    /**
     * Tries to open and lock file, based on $mode.
     *
     * @param string $mode fopen mode - "c" for creating and writing, "r" for reading
     * @return void Doesn't return anything, fills the $fileStream variable of class
     * @throws \Exception In case of fopen failure
     * @throws \Exception In case of flock failure
     */
    public function open(string $mode) : \RCSE\Core\Control\File
    {
        $lock = "";
        
        if (is_dir($this->fileDir) == false) {
            $this->createDir();
        } else {
            $this->setPermissions();
        }

        switch ($mode) {
                case "r":
                    $lock = LOCK_SH;
                    break;
                case "c":
                    $lock = LOCK_EX;
                    break;
        }
        $this->fileStream = fopen($this->fileDir . $this->fileName, $mode."b");
        if ($this->fileStream == false) {
            throw new \Exception("Failed to create file: {$this->fileName}!", 1000);
        }

        if (flock($this->fileStream, $lock) == false) {
            throw new \Exception("Failed to lock the file: {$this->file_path}!", 1001);
        }

        rewind($this->fileStream);
        return $this;
    }

    /**
     * Simply creates directory
     *
     * @return void Returns nothing
     */
    private function createDir()
    {
        mkdir($this->fileDir, $this->filePerms);
    }

    /**
     * Checks, wether target directory is read-\write- able, if not - tries to chmod it
     *
     * @return void Returns nothing
     * @throws \Exception In case of chmod failure
     */
    private function setPermissions()
    {
        if (is_readable($this->fileDir) == false || is_writeable($this->fileDir) == false) {
            if (chmod($this->fileDir, $this->filePerms) == false) {
                throw new \Exception("Failed to set file write-\\read- able: {$this->file_path}!", 1002);
            } elseif (is_readable($this->fileDir) == false || is_writeable($this->fileDir) == false) {
                throw new \Exception("Failed to set file write-\\read- able: {$this->file_path}!", 1002);
            }
        }
    }

    /**
     * Simply unlocks and closes file, also clears stat cache
     *
     * @return void Returns nothing
     */
    private function close()
    {
        clearstatcache();
        flock($this->fileStream, LOCK_UN);
        fclose($this->fileStream);
    }

    /**
     * Tries to read target file
     *
     * @return string Contents of file
     * @throws \Exception In case of fread failure
     */
    public function read() : string
    {
        $file_contents = "";
        $this->open("r");

        $file_contents = fread($this->fileStream, filesize($this->fileDir.$this->fileName));

        if ($file_contents == false) {
            throw new \Exception("Failed to read from file: {$this->file_path}!", 1003);
        }

        $this->close();

        return $file_contents;
    }

    /**
     * Tries to overwrite the whole file at once
     *
     * @return void Returns nothing
     * @throws \Exception In case of fread failure
     */
    public function write(string $contents)
    {
        $this->open("c");

        file_put_contents($this->fileDir. $this->fileName, "");
        
        if (fwrite($this->fileStream, $contents) == false) {
            throw new \Exception("Failed to write to file: {$this->file_path}!", 1004);
        }

        $this->close();
    }

    /**
     * Tries to write a single line. Requires class init with $fileDir and $fileName
     *
     * @param string $contents Content to write
     * @return void Returns nothing
     * @throws \Exception In case of fwrite failure
     */
    public function writeLine(string $contents)
    {
        if (fwrite($this->fileStream, $contents) == false) {
            throw new \Exception("Failed to write line to file: {$this->file_path}!", 1005);
        }
        
        fflush($this->fileStream);
    }
}
