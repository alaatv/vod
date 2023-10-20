<?php

namespace App\Traits;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

trait MigrationProgressBar
{
    private function createBar(int $count): ProgressBar
    {
        $outPut = new ConsoleOutput();
        return new ProgressBar($outPut, $count);
    }
}
