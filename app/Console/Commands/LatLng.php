<?php

namespace App\Console\Commands;

use App\Models\MapDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LatLng extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alaaTv:add-lat-lng';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add all lats and lngs from map details table to lat_lngs table';

    private array $totalLatLngs;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $mapDetails = MapDetail::all();
        foreach ($mapDetails as $mapDetail) {
            match ($mapDetail->type_id) {
                1 => $this->addMarkerDataToLatLngsTable($mapDetail),
                2 => $this->addPolylineDataToLatLngsTable($mapDetail),
            };
        }
        DB::table('lat_lngs')->insert($this->totalLatLngs);
        return 0;
    }

    private function addMarkerDataToLatLngsTable($mapDetail)
    {
        $data = json_decode($mapDetail->getRawOriginal('data'), true);
        $this->totalLatLngs[] = [
            'map_detail_id' => $mapDetail->id,
            'lat' => $data['latlng']['lat'] ?? $data['latlng'][0],
            'lng' => $data['latlng']['lng'] ?? $data['latlng'][1],
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function addPolylineDataToLatLngsTable($mapDetail)
    {
        foreach (json_decode($mapDetail->getRawOriginal('data'))->latlngs as $latlng) {
            $this->totalLatLngs[] = [
                'map_detail_id' => $mapDetail->id,
                'lat' => $latlng->lat,
                'lng' => $latlng->lng,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    }
}
