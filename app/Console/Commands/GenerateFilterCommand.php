<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateFilterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:make:filter {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a new filter class stub.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filterName = $this->argument('name');
        $stub = $this->compileTemplate($filterName);

        $path = $this->makeFilterClass($filterName, $stub);
        $this->info($path.'  created.');
    }

    /**
     * @param $filterName
     *
     * @return bool|mixed|string
     */
    protected function compileTemplate($filterName)
    {
        $stub = file_get_contents(app_path('Console/filter.stub'));
        $stub = str_replace('{{CLASS}}', $filterName, $stub);
        $stub = str_replace('{{ATTRIBUTE_NAME}}', str_slug($filterName, '_'), $stub);

        return $stub;
    }

    /**
     * @param $filterName
     * @param $stub
     *
     * @return string
     */
    protected function makeFilterClass($filterName, $stub): string
    {
        $path = app_path('Classes/Search/Filters/').$filterName.'.php';
        file_put_contents($path, $stub);

        return $path;
    }
}
