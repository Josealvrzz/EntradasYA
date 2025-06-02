<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('entradas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos');
            $table->string('tipo'); // VIP, General, etc.
            $table->decimal('precio', 8, 2);
            $table->integer('cantidad_total');
            $table->integer('cantidad_disponible');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('entradas');
    }
};