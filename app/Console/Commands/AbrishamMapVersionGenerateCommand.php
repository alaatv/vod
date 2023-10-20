<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class AbrishamMapVersionGenerateCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:abrishamMapVersion:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the key for Rahe Abrisham map';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $key = $this->generateRandomKey();

        // Next, we will replace the key for Rahe Abrisham map in the environment file so it is
        // automatically setup for this developer. This key gets generated using a
        // secure random byte generator and is later base64 encoded for storage.
        if (!$this->setKeyInEnvironmentFile($key)) {
            return 0;
        }

        $this->laravel['config']['constants.RAHE_ABRISHAM_MAP_VERSION'] = $key;

        $this->info('Rahe abrisham version set successfully.');
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function generateRandomKey()
    {
        return Carbon::now()->timestamp;
    }

    /**
     * Set the application key in the environment file.
     *
     * @param  string  $key
     * @return bool
     */
    protected function setKeyInEnvironmentFile($key)
    {
        $this->writeNewEnvironmentFileWith($key);

        return true;
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param  string  $key
     * @return void
     */
    protected function writeNewEnvironmentFileWith($key)
    {
        $count = 0;
        file_put_contents($this->laravel->environmentFilePath(), preg_replace(
            $this->keyReplacementPattern(),
            'RAHE_ABRISHAM_MAP_VERSION='.$key,
            file_get_contents($this->laravel->environmentFilePath(),),
            -1,
            $count
        ));

        if ($count == 0) {
            file_put_contents($this->laravel->environmentFilePath(),
                file_get_contents($this->laravel->environmentFilePath(),).'RAHE_ABRISHAM_MAP_VERSION='.$key);
        }
    }

    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern()
    {
        $escaped = preg_quote('='.$this->laravel['config']['constants.RAHE_ABRISHAM_MAP_VERSION'], '/');

        return "/^RAHE_ABRISHAM_MAP_VERSION{$escaped}/m";
    }
}
