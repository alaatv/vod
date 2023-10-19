<?php


namespace App\Classes\Updater;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\DB;

class TextBuilderUpdater
{
    private $builder;
    private string $operation;
    private string $column;
    private string $text;
    private string|null $replacingText;


    public function setQueryBuilder($builder): self
    {
        $this->builder = $builder;
        return $this;
    }

    public function setColumn(string $column): self
    {
        $this->column = $column;
        return $this;
    }

    public function setOperation(string $operation): self
    {
        $this->operation = $operation;
        return $this;
    }

    public function apply($text, $replacingText = null): void
    {
        $this->text = $text;
        $this->replacingText = $replacingText;
        $this->{$this->operation}();
    }

    private function concatStart(): void
    {
        $this->builder->update([$this->column => DB::raw("CONCAT('$this->text ', $this->column)")]);
    }

    private function concatEnd(): void
    {
        $this->builder->update([$this->column => DB::raw("CONCAT($this->column,' $this->text')")]);
    }

    private function replace(): void
    {
        $this->builder->update([$this->column => DB::raw("REPLACE($this->column,'$this->replacingText','$this->text')")]);
    }

    private function delete(): void
    {
        $this->builder->update([$this->column => DB::raw("TRIM(REPLACE($this->column,'$this->text',''))")]);
    }

}
