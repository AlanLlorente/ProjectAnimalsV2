<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Mensajes;
use Faker\Generator as Faker;

$factory->define(Mensajes::class, function (Faker $faker) {
    return [
        'from_users_id' => factory(\App\Usuarios::class),
        'to_users_id' => factory(\App\Usuarios::class),
        'titulo' => $faker->title,
        'contenido'=>$faker->paragraph,
    ];
});
