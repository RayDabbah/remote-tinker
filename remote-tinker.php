#!/usr/bin/php
<?php

use RemoteTinker\Config;
use RemoteTinker\RunOnRemote;
use function Laravel\Prompts\error;
use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;

require __DIR__ . '/vendor/autoload.php';

switch ($argv[1] ?? null) {

    case null:

        (new RunOnRemote())();

        break;

    case 'config':

        Config::setup();

        break;

    default:
        error('Invalid command');

        return 1;


}

