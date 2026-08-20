<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            // Altera a coluna para aceitar nulo
            $table->integer('interval_hours')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            // Reverte caso precise voltar atrás
            $table->integer('interval_hours')->nullable(false)->change();
        });
    }
};