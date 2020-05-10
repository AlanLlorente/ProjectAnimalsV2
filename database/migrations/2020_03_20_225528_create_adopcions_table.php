<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdopcionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adopcions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('usuarios_id');
            $table->string('tipo');
            $table->string('edad');
            $table->string('raza');
            $table->string('cuidad');
            $table->string('provincia');
            $table->string('nombre');
            $table->string('sexo');
            $table->string('detalles');
            $table->string('adoptedby_id')->nullable()->default(null);
            $table->string('image_1')->nullable()->default(null);
            $table->string('image_2')->nullable()->default(null);
            $table->string('image_3')->nullable()->default(null);
            $table->boolean('archived')->default(0);
            $table->timestamps();

            $table->foreign('usuarios_id')
                ->references('user')
                ->on('usuarios')
                ->onDelete('cascade');

            $table->foreign('adoptedby_id')
                ->references('user')
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
        Schema::dropIfExists('adopcions');
    }
}
