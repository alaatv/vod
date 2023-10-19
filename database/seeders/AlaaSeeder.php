<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

abstract class AlaaSeeder extends Seeder
{
    protected string $table;
    protected array $data;

    public function __construct()
    {
        $this->setTable();
        $this->deleteTable();
        $this->setData();
    }

    public function run()
    {
        $this->insertIntoTable();
    }

    protected function deleteTable()
    {
        DB::table($this->table)->delete();
    }

    protected function insertIntoTable()
    {
        DB::table($this->table)->insert($this->data);
    }

    abstract protected function setData(): void;

    abstract protected function setTable(): void;
}
