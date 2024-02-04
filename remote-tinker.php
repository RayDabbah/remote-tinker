#!/usr/bin/php
<?php

use RemoteTinker\Config;
use RemoteTinker\RunOnRemote;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;

require $_composer_autoload_path ?? (__DIR__ . '/vendor/autoload.php');

switch ($argv[1] ?? null) {

    case null:

        (new RunOnRemote())();

        break;

    case 'setup':

        Config::setup();

        break;
    case 'help':
    case '--help':
    case '-h':

        info("Remote tinker help:");
        info("Run a local file in Tinker on a remote server");
        info("help: Display this message");
        info("setup: Set up your remote servers");

        break;

    default:
        error('Invalid command. Try --help');

        return 1;


}

