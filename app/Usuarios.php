<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    //Conseguir todas las adopciones creadas
    public function adopciones()
    {
        return $this->hasMany('App\Adopcion');
    }

    //Conseguir todos los mensajes de un usuario
    public function mensajes()
    {
        return $this->hasMany('App\Mensajes');
    }
}
