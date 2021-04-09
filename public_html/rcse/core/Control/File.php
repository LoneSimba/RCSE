<?php
declare(strict_types=1);

namespace RCSE\Core\Control;

use Exception;
use RCSE\Core\Statics\GlobalArrays;

/** File Handler, provides functions to write and read files */
class File
{
    private $fileStream;
    private string $fileName;
    private string $fileDir;
    private string $rootDir;
    private int $filePerms = 0777;

    /**
     *
     * @param string $fileDir File directory w\o ROOT directory
     * @param string $fileName Filename
     */
    public function __construct(string $fileDir, string $fileName)
    {
        $this->rootDir = (string) GlobalArrays::getServerArrayEntry('DOCUMENT_ROOT');
        $this->fileDir = $this->rootDir . $fileDir;
        $this->fileName = $fileName;
    }

    public function __destruct()
    {
        if (is_resource($this->fileStream)) {
            $this->close();
        }
    }

    /**
     * Tries to open and lock file, based on $mode.
     *
     * @param string $mode fopen mode - "w"/"a+" for creating and writing, "r" for reading
     * @return self
     * @throws Exception
     */
    public function open(string $mode): self
    {
        if (!preg_match("/(?i)a\+|r|w/", $mode)) {
            throw new Exception("File open mode should either be w, a+ or r, {$mode} used instead.", 0x000105);
        }

        $lock = "";
        
        if (is_dir($this->fileDir) == false) {
            mkdir($this->fileDir, $this->filePerms);
        } else {
            $this->setPermissions();
        }

        switch ($mode) {
            case "r":
                $lock = LOCK_SH;
                break;
            case "w":
            case "a+":
                $lock = LOCK_EX;
                break;
        }

        $this->fileStream = fopen($this->fileDir . $this->fileName, $mode);
        if ($this->fileStream == false) {
            //$this->log->log('Error', "Failed to create or open file: {$this->fileName}!", self::class);
            //@todo Exception should be replaced with FileNotFoundException
            throw new Exception("Failed to create or open file: {$this->fileName}!", 0x000100);
        }

        if (flock($this->fileStream, $lock) == false) {
            //$this->log->log('Error', "Failed to lock file: {$this->fileName}!", self::class);
            //@todo Exception should be replaced with ObtainFileLockException
            throw new Exception("Failed to lock file: {$this->fileDir}.{$this->fileName}!", 0x000101);
        }

        rewind($this->fileStream);
        return $this;
    }

    /**
     * Tries to read target file
     *
     * @return string Contents of file
     * @throws Exception
     */
    public function read(): string
    {
        $this->open("r");

        $file_contents = fread($this->fileStream, fstat($this->fileStream)['size']);

        if ($file_contents == false) {
            //$this->log->log('Error', "Failed to read file contents: {$this->fileDir}{$this->fileName}!", self::class);
            //@todo Should be replaced with FileReadingFailedException
            throw new Exception("Failed to read file contents: {$this->fileDir}{$this->fileName}!", 0x000103);
        }

        $this->close();

        return $file_contents;
    }

    /**
     * Tries to overwrite the whole file at once. Caution, use with read(), or original contents will be lost!
     *
     * @param string $contents
     * @return bool True if succeeds
     * @throws Exception
     */
    public function write(string $contents): bool
    {
        $this->open("w");

        if (!fwrite($this->fileStream, $contents)) {
            //$this->log->log('Error', "Failed to write file: {$this->fileDir}{$this->fileName}!", self::class);
            //@todo Should be replaced with FileWritingFailedException
            throw new Exception("Failed to write file: {$this->fileDir}{$this->fileName}!", 0x000104);
        }

        $this->close();
        return true;
    }

    /**
     * Writes a single line.
     *
     * @param string $contents Content to write
     * @return bool True is succeeds
     * @throws Exception
     */
    public function writeLine(string $contents): bool
    {
        if (!fwrite($this->fileStream, $contents)) {
            //$this->log->log('Error', "Failed to write line to file: {$this->fileDir}{$this->fileName}!", self::class);
            //@todo Should be replaced with FileWritingFailedException
            throw new Exception("Failed to write line to file: {$this->fileDir}{$this->fileName}!", 0x000104);
        }

        fflush($this->fileStream);
        return true;
    }

    /**
     * Checks, whether target directory is read-\write- able, if not - tries to chmod it
     *
     * @return void
     * @throws Exception In case of chmod failure
     */
    private function setPermissions(): void
    {
        if (!is_readable($this->fileDir) || !is_writeable($this->fileDir)) {
            if ((!chmod($this->fileDir, $this->filePerms)) ||
                (!is_readable($this->fileDir) || !is_writeable($this->fileDir)))
            {
                //$this->log->log('Error', "Failed to set file permissions: {$this->fileDir}{$this->fileName}!", self::class);
                //@todo Exception should be replaced with FileInaccessibleException
                throw new Exception("Failed to set file permissions: {$this->fileDir}{$this->fileName}!", 0x000102);
            }
        }
    }

    /**
     * Releases the lock and closes file
     *
     * @return void 
     */
    private function close(): void
    {
        clearstatcache();
        fflush($this->fileStream);
        flock($this->fileStream, LOCK_UN);
        fclose($this->fileStream);
    }

}
