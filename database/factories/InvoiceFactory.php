<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Sales\Invoice;
use Faker\Generator as Faker;

$factory->define(Invoice::class, function (Faker $faker) {
    return [
        'invoice_number' => 'INVOICE'.$faker->numberBetween($min = 1000, $max = 2000),
        'status' => 'sent',
        'invoiced_year' => $faker->numberBetween($min = 2074, $max = 2077),
        'invoiced_month' => $faker->numberBetween($min = 1, $max = 12),
        'invoiced_day' => $faker->numberBetween($min = 1, $max = 32),
        'amount' => $faker->numberBetween($min = 1000, $max = 10000),
        'category_id'=>3,
        'customer_id' => $faker->randomElement(App\Models\Contact::where('type', 'customer')->pluck('id'))
    ];
});
