<?php

use Faker\Generator as Faker;

$factory->define(App\Userbon::class, function (Faker $faker) {
    return [
        'bon_id' => 1,
        'totalNumber' => 10,
        'usedNumber' => 0,
        'userbonstatus_id' => 1,
    ];
});
