<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\MorphRegistration\Models\Entities\Registration;

$factory->define(Registration::class, function (Faker $faker) {
    return [
        'state' => 0
    ];
});
