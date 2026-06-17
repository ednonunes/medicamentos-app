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
        Schema::create('medication_logs', function (Blueprint $table) {
            $table->id();
            // Relaciona com o medicamento (se o remédio for deletado, os logs somem em cascata)
            $table->foreignId('medication_id')->constrained()->onDelete('cascade');
            // Guarda o horário teórico que estava agendado (ex: "14:00")
            $table->string('scheduled_time');
            // Guarda o momento exato em que o usuário clicou no botão
            $table->timestamp('taken_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medication_logs');
    }
};
