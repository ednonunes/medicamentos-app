<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            // text permite textos longos para instruções médicas ou cuidados
            $table->text('observations')->nullable()->after('start_time'); 
            
            // boolean funciona como a flag (0 para falso, 1 para verdadeiro)
            $table->boolean('take_on_empty_stomach')->default(false)->after('observations');
        });
    }

    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            $table->dropColumn(['observations', 'take_on_empty_stomach']);
        });
    }
};