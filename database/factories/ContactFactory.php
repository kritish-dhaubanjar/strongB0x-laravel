<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Contact;
use Faker\Generator as Faker;

$factory->define(Contact::class, function (Faker $faker) {
    return [
        'type' => $faker->randomElement($array = ['vendor', 'customer']),
        'name' => $faker->name,
        'opening_balance' => $faker->numberBetween($min = 0, $max = 10000),
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->phoneNumber,
        'address'=> $faker->address,
    ];
});
