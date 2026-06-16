<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            // Salva ex: ["Seg", "Ter", "Qui"]. Por padrão, se for nulo, assume todos os dias.
            $table->json('days_of_week')->nullable()->after('interval_hours');
        });
    }

    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->dropColumn('days_of_week');
        });
    }

};
