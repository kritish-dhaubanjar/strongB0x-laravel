<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Purchases\Bill;
use Faker\Generator as Faker;

$factory->define(Bill::class, function (Faker $faker) {
    return [
        'bill_number' => 'BILL'.$faker->numberBetween($min = 1000, $max = 2000),
        'status' => 'received',
        'billed_year' => $faker->numberBetween($min = 2074, $max = 2077),
        'billed_month' => $faker->numberBetween($min = 1, $max = 12),
        'billed_day' => $faker->numberBetween($min = 1, $max = 32),
        'amount' => $faker->numberBetween($min = 1000, $max = 10000),
        'category_id'=>3,
        'vendor_id' => $faker->randomElement(App\Models\Contact::where('type', 'vendor')->pluck('id'))
    ];
});
