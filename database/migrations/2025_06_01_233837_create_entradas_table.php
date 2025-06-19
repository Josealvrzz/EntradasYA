<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradasTable extends Migration
{
    public function up()
    {
        Schema::create('entradas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->onDelete('cascade');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->string('codigo')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('entradas');
    }
}