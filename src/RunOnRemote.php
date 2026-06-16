<?php

namespace RemoteTinker;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

class RunOnRemote
{

    private Config $config;


    public function __construct()
    {

        $this->config = new Config();
    }

    public function __invoke(array $flags = []): void
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

        $returnFiles = $this->shouldReturnFiles($flags, $locationData);

        $envPrefix = '';
        $outputDirRemote = null;

        if ($returnFiles) {
            $outputDirRemote = '/tmp/remote-tinker-out-' . sha1(microtime(true));
            `ssh {$user}@$url "mkdir -p {$outputDirRemote}"`;
            $envPrefix = "REMOTE_TINKER_OUTPUT_DIR={$outputDirRemote} ";
        }

        echo `scp {$data['filePath']} {$user}@$url:$fileDestination/$fileName`;

        $runCommand = <<<COMMAND
                    ssh {$user}@$url  "cd $fileDestination && {$envPrefix}php artisan tinker --execute='require(\"{$fileName}\")'"
                    COMMAND;

        spin(fn() => info((string)`$runCommand`), 'Executing your code. Hold tight...');

        echo `ssh {$user}@$url  "rm {$fileDestination}/{$fileName}"`;

        if ($returnFiles) {
            $this->pullOutputFiles($user, $url, $outputDirRemote, $locationData);
        }

        note('Done!');
    }

    private function shouldReturnFiles(array $flags, array $locationData): bool
    {
        if (in_array('--no-return-files', $flags, true)) {
            return false;
        }

        if (in_array('--return-files', $flags, true)) {
            return true;
        }

        if (!empty($locationData['alwaysReturnFiles'])) {
            return true;
        }

        return confirm('Pull output files back from the remote?', default: false);
    }

    private function pullOutputFiles(string $user, string $url, string $outputDirRemote, array $locationData): void
    {
        $localDest = $locationData['localOutputDir'] ?? getcwd();

        if (!is_dir($localDest)) {
            mkdir($localDest, 0755, recursive: true);
        }

        $hasFiles = trim((string)`ssh {$user}@$url "ls -A {$outputDirRemote} 2>/dev/null"`);

        if ($hasFiles !== '') {
            echo `scp -r {$user}@$url:{$outputDirRemote}/. {$localDest}/`;
            info("Output files pulled to: {$localDest}");
        } else {
            warning('No output files were written by the script.');
        }

        `ssh {$user}@$url "rm -rf {$outputDirRemote}"`;
    }


}
