<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id'); // Adiciona a coluna
        });

        // udate users:
        $users = DB::table('users')->whereNull('uuid')->get();
        foreach ($users as $user) {
            DB::table('users')->where('id', $user->id)->update([
                'uuid' => \Illuminate\Support\Str::uuid()
            ]);
        }
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
