<?php

use Cintas\Models\Facility\LaundryCustomer;
use Faker\Generator as Faker;

$factory->define(LaundryCustomer::class, function (Faker $faker) {
    return [
        'cuid' => \EndyJasmi\Cuid::make(),
        'name' => $faker->company,
        'customer_id' => config('cintas.customer_cuid')
    ];
});
