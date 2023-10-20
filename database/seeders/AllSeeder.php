<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

abstract class AllSeeder extends Seeder
{
    protected array $allSeederInThisDirectory = [];
    abstract protected function getDirectory():string;
    abstract protected function getDNameSpace():string;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->populateSeeders();
        $this->call($this->allSeederInThisDirectory);
    }


    private function populateSeeders()
    {
        foreach (File::files($this->getDirectory()) as $file) {
            $className = $file->getFilenameWithoutExtension();
            $fullClassName = $this->getDNameSpace().'\\'.$className;
            if( $fullClassName != get_class($this)){
                $this->allSeederInThisDirectory[] =  $fullClassName;
            }
        }
    }

}
