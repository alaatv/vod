<?php

use Faker\Generator as Faker;

$factory->define(App\Bon::class, function (Faker $faker) {
    return [
        'name'=>$faker->title,
        'displayName'=>$faker->title,
        'bontype_id'=>null,
        'description'=>$faker->paragraph,
        'isEnable'=>1
    ];
});
