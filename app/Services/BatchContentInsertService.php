<?php

namespace App\Services;

use App\Jobs\BulkInsertContents;
use App\Jobs\BulkInsertSets;

class BatchContentInsertService
{
    public int $type;
    public string $fileName;
    public int $productId;
    public int $insertId;

    public function setType(int $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function setFileName($fileName): static
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function setProductId(int $productId): static
    {
        $this->productId = $productId;
        return $this;
    }

    public function setInsertId(int $insertId): static
    {
        $this->insertId = $insertId;
        return $this;
    }

    public function upload(): void
    {
        match ($this->type) {
            1 => $this->bulkInsertSets(),
            2 => $this->bulkInsertContents()
        };
    }

    private function bulkInsertSets(): void
    {
        BulkInsertSets::dispatch($this->fileName, $this->productId, $this->insertId);
    }

    private function bulkInsertContents(): void
    {
        BulkInsertContents::dispatch($this->fileName, $this->productId, $this->insertId);
    }
}
