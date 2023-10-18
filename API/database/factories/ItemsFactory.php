<?php

use Faker\Generator as Faker;

$factory->define(\Cintas\Models\Items\Item::class, function (Faker $faker) {
    $date = $faker->dateTimeThisYear($max = 'now', $timezone = 'UTC')->format('y-m-d h:m:s');
    $delDate = $faker->optional(0.1)->dateTimeThisYear($max = '- 2 months', $timezone = 'UTC');
    $delDate = $delDate ? $delDate->format('y-m-d h:m:s') : null;
    return [
        'cuid' => \EndyJasmi\Cuid::make(),
        'created_at' => (new Carbon\Carbon($date))->timezone('UTC')->toDateTimeString(),
        'updated_at' => (new Carbon\Carbon($date))->timezone('UTC')->toDateTimeString(),
        'deleted_at' => $delDate ? (new Carbon\Carbon($delDate))->timezone('UTC')->toDateTimeString() : null,
        'cycle_count' => $delDate ? $faker->numberBetween(20, 50) : $faker->numberBetween(0, 40),
        'product_id' => \Cintas\Models\Items\Product::all()->random()->cuid,
        'customer_id' => config('cintas.customer_cuid')
    ];
});
