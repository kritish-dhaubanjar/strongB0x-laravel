<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Item;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Item::class, function (Faker $faker) {
    return [
        'name' => Str::random(16),
        'description' => $faker->paragraph,
        'sale_price' => $faker->numberBetween($min = 1, $max = 100),
        'purchase_price' => $faker->numberBetween($min = 1, $max = 100),
        'unit_id'=> $faker->numberBetween($min=1, $max = 5),
        'category_id'=> 4,
        'tax_id'=>2
    ];
});
