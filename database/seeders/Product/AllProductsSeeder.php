<?php

namespace Database\Seeders\Product;

use Database\Seeders\Orders\CanceledOrderSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class AllProductsSeeder extends Seeder
{
    private array $allSeederInThisDirectory = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $this->populateSeeders();
        $this->call($this->allSeederInThisDirectory);
        Schema::enableForeignKeyConstraints();
    }

    private function populateSeeders()
    {
        foreach (File::files(__DIR__) as $file) {
            $className = $file->getFilenameWithoutExtension();
            $fullClassName = __NAMESPACE__.'\\'.$className;
            if( $fullClassName != get_class($this)){
                $this->allSeederInThisDirectory[] =  $fullClassName;
            }
        }
    }

}
