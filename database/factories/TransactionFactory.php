<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Transaction;
use Faker\Generator as Faker;

$factory->define(Transaction::class, function (Faker $faker) {
    $type = $faker->randomElement(['income' ,'expense']);
    $contact_type = $type == 'income' ? 'customer' : 'vendor';
    $contact_ids = App\Models\Contact::where('type', $contact_type)->pluck('id');
    return [
        'type' => $type,
        'paid_year' => $faker->numberBetween($min = 2074, $max = 2077),
        'paid_month' => $faker->numberBetween($min = 1, $max = 12),
        'paid_day' => $faker->numberBetween($min = 1, $max = 32),
        'amount'=> $faker->numberBetween($min=100, $max = 1000),
        'account_id'=> $faker->numberBetween($min=1, $max = 2),
        'contact_id'=> $faker->randomElement($contact_ids),
        'category_id'=> $type == 'income' ? $faker->randomElement([1,2]) : 3,
        'payment_method'=>$faker->randomElement(['Cash', 'Bank Transfer', 'Cheque'])
    ];
});
