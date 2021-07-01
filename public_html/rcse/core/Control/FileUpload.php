<?php
namespace RCSE\Core\Control;


class FileUpload
{
    private array $filesArray;
    private array $fileTypes;
    private array $uploaderConf;

    public function __construct()
    {
        $this->uploaderConf = (new Config())->getConfig("uploader");
    }
}