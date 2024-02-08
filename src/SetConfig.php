<?php

namespace RemoteTinker;

use function Laravel\Prompts\text;
use function Laravel\Prompts\info;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\select;
use function Laravel\Prompts\confirm;

class SetConfig
{

    private Config $config;


    public function __construct()
    {

        $this->config = new Config();
    }

    public function setup(): void
    {


        $remotes = $this->config->get('remotes') ?? [];

        if (!count($remotes)) {
            info("Looks like you have no remote servers setup. Let's set them up now...");

            $this->setRemote();

            return;

        } else {


            $env = select('Please select what you would like to do...', collect(['Add New','Update', 'Delete']));

            switch ($env) {
                case 'Add New':
                    $this->setRemote();
                    break;

                case 'Delete':
                    $this->deleteRemote();
                    break;

                case 'Update':
                    $this->updateRemote();
            }

        }


    }

    public function setRemote(?string $name = null): void
    {

        $serverName = $name ?: text('What is the name of the server you want to add?', required: true);

        $url = text("What is the url/ip for $name?", required: true);

        $directory = text('What is the path to the directory where your Laravel project where you will be running your scripts? (Relative to the home directory of your user)', required: true);

        $user = text("What is the name of the user to log in to $name?", required: true);

        $isProduction = confirm('Is this a production environment?', default: false);

        $this->config->set('remotes.' . $serverName, compact('url', 'user', 'isProduction', 'directory'));

        outro(ucfirst($serverName . ' set successfully!'));


    }

    private function deleteRemote()
    {

        $env = select('Which server would you like to delete?', array_keys($this->config->get('remotes')), required: true);

        $this->config->remove("remotes.$env");

        outro(ucfirst($env . ' removed successfully!'));


    }




    private static function remove($key, $value)
    {


    }

    private function updateRemote(): void
    {
        $env = select('Which server would you like to update?', array_keys($this->config->get('remotes')), required: true);

        $this->setRemote($env);
    }


}