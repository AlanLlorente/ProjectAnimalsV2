<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Adopcion;
use Faker\Generator as Faker;

$factory->define(Adopcion::class, function (Faker $faker) {
    return [
        'usuarios_id' => factory(\App\Usuarios::class),
        'tipo'  => $faker->name,
        'edad'   => 7,
        'raza' => $faker->name,
        'cuidad' => $faker->city,
        'provincia' => $faker->country,
        'detalles' => $faker->sentence
    ];
});
