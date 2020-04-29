<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Item;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Item::class, function (Faker $faker) {
	$faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
	$purchase_price = $faker->numberBetween($min = 1, $max = 100);

    return [
        // 'name' => Str::random(16),
        'name' => $faker->productName,
        'description' => $faker->paragraph,
        'sale_price' => $purchase_price + $faker->numberBetween($min = 1, $max = 100),
        'purchase_price' => $purchase_price,
        'unit_id'=> $faker->numberBetween($min=1, $max = 5),
        'category_id'=> 4,
        'tax_id'=>2
    ];
});
