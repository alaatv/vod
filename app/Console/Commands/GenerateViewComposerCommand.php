<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateViewComposerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:composer {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new view composer class stub.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $composerName = $this->argument('name');
        $stub = $this->compileTemplate($composerName);

        $path = $this->makeFilterClass($composerName, $stub);
        $this->info($path.'  created.');
    }

    /**
     * @param $composerName
     *
     * @return bool|mixed|string
     */
    protected function compileTemplate($composerName)
    {
        $stub = file_get_contents(app_path('Console/viewComposer.stub'));
        $stub = str_replace('{{CLASS}}', $composerName, $stub);

        return $stub;
    }

    /**
     * @param $composerName
     * @param $stub
     *
     * @return string
     */
    protected function makeFilterClass($composerName, $stub): string
    {
        $path = app_path('Http/ViewComposers/').$composerName.'Composer.php';
        file_put_contents($path, $stub);

        return $path;
    }
}
