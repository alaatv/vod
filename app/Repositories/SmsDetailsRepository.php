<?php


namespace App\Repositories;


use App\Models\SmsDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SmsDetailsRepository
{
    public Builder $query;

    public function __construct()
    {
        $this->query = SmsDetail::query();
    }

    public function filterBy(array $filters): SmsDetailsRepository
    {
        foreach ($filters as $key => $value) {
            $this->query->where($key, $value);
        }
        return $this;
    }

    public function createdAfter($date): SmsDetailsRepository
    {
        $this->query->where('created_at', '>', $date);
        return $this;
    }

    public function createdBefore($date): SmsDetailsRepository
    {
        $this->query->where('created_at', '<', $date);
        return $this;
    }

    public function with(array $relations)
    {
        $this->query->with($relations);
        return $this;
    }

    public function get(): Collection
    {
        return $this->query->get();
    }
}
