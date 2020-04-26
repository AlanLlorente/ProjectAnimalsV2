<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Adopcion extends Model
{
    public function user()
    {
        return $this->belongsTo(Usuarios::class);
    }

    public function adoptedby()
    {
        return $this->belongsTo(Usuarios::class);
    }
}
