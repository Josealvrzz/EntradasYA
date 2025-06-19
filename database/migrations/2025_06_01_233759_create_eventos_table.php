<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventosTable extends Migration
{
    public function up()
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->dateTime('fecha');
            $table->foreignId('lugar_id')->constrained('lugares')->onDelete('cascade');
            $table->integer('capacidad');
            $table->enum('estado', ['activo', 'finalizado', 'cancelado'])->default('activo');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('eventos');
    }
}