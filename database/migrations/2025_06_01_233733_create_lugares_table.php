<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLugaresTable extends Migration
{
    public function up()
    {
        Schema::create('lugares', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->integer('capacidad');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lugares');
    }
}