<?php

use Cintas\Models\Facility\Reader;
use Faker\Generator as Faker;

$factory->define(Reader::class, function (Faker $faker) {
    $ids = ['Gate', 'Table', 'Dryer'];
    return [
        'cuid' => \EndyJasmi\Cuid::make(),
        'id' => $faker->randomElement($ids) . ' ' . $faker->unique()->randomDigitNotNull,
        'ip_address' => $faker->localIpv4,
        'customer_id' => config('cintas.customer_cuid')
    ];
});
