<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMensajesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('from_users_id');
            $table->unsignedBigInteger('to_users_id');
            $table->mediumText('titulo');
            $table->longText('contenido');
            $table->boolean('leido')->default(0);
            $table->boolean('borrar')->default(0);
            $table->timestamps();


            $table->foreign('from_users_id')
                ->references('id')
                ->on('usuarios');

            $table->foreign('to_users_id')
                ->references('id')
                ->on('usuarios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mensajes');
    }
}
