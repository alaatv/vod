<?php


namespace App\Classes;


use Laravel\Octane\Cache\OctaneStore;

class OctaneStoreGroupForget extends OctaneStore
{
    public function forgetByPrefix($prefix)
    {
        foreach ($this->table as $key => $record) {
            if (str_starts_with($key, $prefix)) {
                $this->forget($key);
            }
        }
        return true;
    }
}
