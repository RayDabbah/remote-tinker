<?php

namespace RemoteTinker;

class Config
{


    private array $data;

    private $configFilePath;

    public function __construct()
    {

        $this->setup();

    }


    public function get($key = null)
    {

        return array_key_exists($key, $this->data) ? $this->data[$key] : $this->data;
    }

    public function set($key, $data): void
    {


        $this->data = [...$this->data, $key => $data];

        file_put_contents($this->configFilePath, json_encode($this->data));

    }

    private function setup(): void
    {

        $directory = getenv("HOME") . '/.config/remote-tinker';

        $this->configFilePath = "$directory/remote-tinker.json";

        if (!is_dir($directory)) {
            mkdir($directory, recursive: true);
        }

        if (!is_file($this->configFilePath)) {
            touch($this->configFilePath);
        }

        $this->data = json_decode(file_get_contents($this->configFilePath), true) ?: [];
    }

}