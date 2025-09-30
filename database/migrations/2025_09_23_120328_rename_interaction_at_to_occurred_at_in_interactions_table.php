<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter la nouvelle colonne occurred_at avec DEFAULT CURRENT_TIMESTAMP
        Schema::table('interactions', function (Blueprint $table) {
            $table->dateTime('occurred_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });

        // Copier les valeurs existantes de interaction_at
        DB::statement('UPDATE interactions SET occurred_at = interaction_at');

        // Supprimer l’ancienne colonne interaction_at
        Schema::table('interactions', function (Blueprint $table) {
            $table->dropColumn('interaction_at');
        });
    }

    public function down(): void
    {
        // Recréer l'ancienne colonne
        Schema::table('interactions', function (Blueprint $table) {
            $table->date('interaction_at')->nullable();
        });

        // Copier les valeurs depuis occurred_at
        DB::statement('UPDATE interactions SET interaction_at = occurred_at');

        // Supprimer la colonne occurred_at
        Schema::table('interactions', function (Blueprint $table) {
            $table->dropColumn('occurred_at');
        });
    }
};
