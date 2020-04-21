<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Usuarios;
use Faker\Generator as Faker;

$factory->define(Usuarios::class, function (Faker $faker) {
    return [
        'user' => $faker->userName,
        'nombre' => $faker->name,
        'apellidos' => $faker->firstName,
        'email' => $faker->email,
        'password' => $faker->password,
        'telefono' => $faker->phoneNumber,
        'image' => null,
    ];
});
