<?php

use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {

	static $password;

    return [
    	'id'	=> $faker->numberBetween($min = 1, $max = 9000),
        'username' => $faker->email,
        'password' => $password ?: $password = Hash::make('secret'),
        'role' => 'ADMIN_USER',
        'is_active' => true,
        'remember_token' => str_random(10),
    ];
});
