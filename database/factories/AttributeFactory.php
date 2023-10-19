<?php

use Faker\Generator as Faker;

$factory->define(App\Attribute::class, function (Faker $faker) {
    return [
        'attributecontrol_id'=>3,
        'name'=>$faker->title,
        'displayName'=>$faker->title,
        'description'=>$faker->paragraph,
        'attributetype_id'=>1
    ];
});
