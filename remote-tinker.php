#!/usr/bin/php
<?php

use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;

require __DIR__ . '/vendor/autoload.php';

var_dump($argv, $argc);



(new \RemoteTinker\RunOnRemote())();

