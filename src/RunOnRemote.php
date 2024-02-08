<?php

namespace RemoteTinker;

use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;

class RunOnRemote
{

    private Config $config;


    public function __construct()
    {

        $this->config = new Config();
    }

    public function __invoke(): void
    {

        $config = $this->config->get()['remotes'] ?? null;


        if(!$config){
            (new SetConfig())->setup();
        }


        $data = GetInputs::run($config);

        if (!$data) {
            return;
        }

        $locationData = $config[$data['env']];

        $user = $locationData['user'];

        $fileDestination = sprintf("/home/%s/%s", $user, $locationData['directory']);

        $fileName = sha1(microtime(true)) . '.php';

        $url = $locationData['url'];

        echo `scp {$data['filePath']} {$user}@$url:$fileDestination/$fileName`;

        $runCommand = <<<COMMAND
                    ssh {$user}@$url  "cd $fileDestination && php artisan tinker --execute='require(\"{$fileName}\")'"
                    COMMAND;

        spin(fn() => info((string)`$runCommand`), 'Executing your code. Hold tight...');

        echo `ssh {$user}@$url  "rm {$fileDestination}/{$fileName}"`;

        note('Done!');
    }


}