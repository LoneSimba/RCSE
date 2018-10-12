<?php
declare(strict_types=1);
namespace RCSE\Core;

class FileNotFoundException extends Exception
{
    private
        $file_path;

    public function __construct(string $file_path) 
    {
        parent::__construct("File not found: " . $file_path, 404);
        $this->file_path = $file_path;
    }
}