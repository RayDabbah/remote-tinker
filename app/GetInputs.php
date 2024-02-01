<?php

namespace App;

use function Laravel\Prompts\info;
use function Laravel\Prompts\select;
use function Laravel\Prompts\warning;

class GetInputs
{

    public static function run($config): ?array
    {

        $root = getcwd();

        $env = select('Please select the application for this file to run on...', collect($config)->keys());

        $filePath = null;

        while (!isset($filePath)) {

            $filePathBeginning ??= $root;

            $options = collect(glob($filePathBeginning . '/*'))
                ->filter(fn($path) => is_dir($path) || str_ends_with($path, '.php'))
                ->mapWithKeys(fn($option) => [$option => str_replace($root . '/', '', $option)])
                ->when($filePathBeginning !== $root, fn($optionsList) => $optionsList->merge(['back' => 'Go Back']))
                ->toArray();

            $selected = select('Please select a file', options: $options, scroll: 25);

            info($selected . ' was selected...');

            if ($selected === 'back') {
                $filePathBeginning = preg_replace('/(\/\w*$)/m', '', $filePathBeginning);

            } elseif (is_dir($selected)) {

                $filePathBeginning = $selected;

            } else {
                
                $filePath = $selected;
            }

        }

        if (!$filePath) {

            warning('No file selected');

            return null;

        }

        info("The selected file is $filePath");

        return compact('filePath', 'env');
    }
}