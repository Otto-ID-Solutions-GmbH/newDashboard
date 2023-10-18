<?php

use Faker\Generator as Faker;

$factory->define(\Cintas\Models\Items\Product::class, function (Faker $faker) {
    return [
        'cuid' => \EndyJasmi\Cuid::make(),
        'name' => $faker->unique()->cintasProduct,
        'product_number' => $faker->optional()->ean8,
        'expected_lifetime' => $faker->optional(0.1)->numberBetween(20, 80),
        'product_type_id' => \Cintas\Models\Items\ProductType::all()->random()->cuid,
        'customer_id' => config('cintas.customer_cuid')
        //TODO: Add relation to laundry customer
    ];
});

$factory->define(\Cintas\Models\Items\ProductType::class, function (Faker $faker) {
    return [
        'cuid' => \EndyJasmi\Cuid::make(),
        'name' => $faker->unique()->cintasProductType,
        'expected_lifetime' => $faker->numberBetween(20, 80),
        'customer_id' => config('cintas.customer_cuid')
    ];
});