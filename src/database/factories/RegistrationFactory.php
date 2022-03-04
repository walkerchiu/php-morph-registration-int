<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\MorphRegistration\Models\Entities\Registration;

$factory->define(Registration::class, function (Faker $faker) {
    return [
        'morph_type' => 'WalkerChiu\Group\Models\Entities\Group',
        'morph_id'   => 1,
        'user_id'    => 1,
        'state'  => 0
    ];
});
