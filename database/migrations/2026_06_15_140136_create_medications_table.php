<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('medications', function (Blueprint $table) {
        $table->id();
        // Vincula o medicamento ao usuário que está logado
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        $table->string('name');            // Nome do remédio
        $table->string('dosage');          // ex: 500mg, 10 gotas
        $table->integer('interval_hours'); // ex: 8, 12
        $table->time('start_time');        // Horário da primeira dose
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
