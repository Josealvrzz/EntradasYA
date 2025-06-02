<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('entrada_id')->constrained('entradas');
            $table->integer('cantidad');
            $table->decimal('total', 10, 2);
            $table->string('estado')->default('pagado'); // pagado, reembolsado, etc.
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('ventas');
    }
};