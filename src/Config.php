<?php

namespace RemoteTinker;

class Config
{

    private string $configFilePath;

    private array $data;

    public function __construct()
    {

        $directory = getenv("HOME") . '/.config';

        $this->configFilePath = "$directory/remote-tinker.json";

        if (!is_dir($directory)) {
            mkdir($directory);
        }

        if (!is_file($this->configFilePath)) {
            touch($this->configFilePath);
        }
    }

    public static function get($key)
    {

        return
    }

}