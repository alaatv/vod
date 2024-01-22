<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Class DonateResource
 */
class DonateResource extends AlaaJsonResource
{
    private $latestDonors;

    private $maxDonors;

    private $chartData;

    private $totalIncome;

    private $totalSpend;

    /**
     * Create a new resource instance.
     *
     * @param mixed $resource
     * @return void
     */
    public function __construct($resource, $latestDonors, $maxDonors, $chartData, $totalIncome, $totalSpend)
    {
        // Ensure you call the parent constructor
        parent::__construct($resource);
        $this->resource = $resource;

        $this->latestDonors = $latestDonors;
        $this->maxDonors = $maxDonors;
        $this->chartData = $chartData;
        $this->totalIncome = $totalIncome;
        $this->totalSpend = $totalSpend;
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     */
    public function toArray($request): array
    {
        return [
            'latest_donors' => $this->latestDonors,
            'max_donors' => $this->maxDonors,
            'chart_data' => $this->chartData,
            'total_income' => $this->totalIncome,
            'total_spend' => $this->totalSpend,
        ];
    }
}
