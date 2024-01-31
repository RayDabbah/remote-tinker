<?php

use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;

require __DIR__ . '/vendor/autoload.php';
$config = (require __DIR__ . '/config.php')['remotes'];
$data = \App\GetInputs::run($config);
if (!$data) {
    return;
}
$locationData = $config[$data['env']];
$user = $locationData['user'];
$fileDestination = sprintf("/home/%s/%s", $user, $locationData['directory']);

$fileName = sha1(microtime(true)) . '.php';

$url = $locationData['url'];

echo `scp {$data['filePath']} {$user}@$url:$fileDestination/$fileName`;

$runCommand =  <<<COMMAND
ssh {$user}@$url  "cd $fileDestination && php artisan tinker --execute='require(\"{$fileName}\")'"
COMMAND;


spin(fn() => info(`$runCommand`), 'Executing your code. Hold tight...');



echo `ssh {$user}@$url  "rm {$fileDestination}/{$fileName}"`;


note('Done!');

