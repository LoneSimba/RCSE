<?php
declare(strict_types=1);

namespace RCSE\Core\Control;

class Config
{
    private $configFileDir = "/config/";
    private $configFileName = "config.json";
    private $file;


    public function __construct()
    {
        $this->file = (new File($this->configFileDir, $this->configFileName))->open("r");
    }

    public function getConfig(string $type) : array
    {
        $contents = json_decode($this->file->read());

        return (array_key_exists($type, $contents)) ? $contents[$type] : [];
        
    }

    public function setConfig(array $config) : bool
    {
        
    }

    public function createConfig(array $settings) : bool
    {
        $defaultConfig = [
            'database' => [
                'host' => '',
                'user' => '',
                'pass' => '',
                'port' => '',
                'name' => ''
            ],
            'main' => [
                'sitename' => '',
                'description' => '',
                'keywords' => '',
                'installed' => '',
                'offline' => '',
                'theme' => '',
                'lang' => '',
                'startPage' => '',
                'plugins' => ''
            ]
        ];

        foreach($settings as $key => $value) {
            $defaultConfig[$key] = $value;
        }
    }
}