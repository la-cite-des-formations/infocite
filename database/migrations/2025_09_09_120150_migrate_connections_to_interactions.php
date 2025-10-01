<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifie que la table connections existe
        if (Schema::hasTable('connections')) {
            // Transfert des données vers interactions
            DB::table('connections')->orderBy('id')->chunk(100, function ($connections) {
                foreach ($connections as $connection) {
                    DB::table('interactions')->insert([
                        'user_id'        => $connection->user_id,
                        'target_id'      => null,          // pas de cible
                        'target_type'    => null,
                        'type'           => 'connection',  // type de l'action
                        'interaction_at' => $connection->connected_at,
                    ]);
                }
            });

            // Suppression de la table connections
            Schema::dropIfExists('connections');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionnel : recréer la table connections à partir des interactions
        if (!Schema::hasTable('connections')) {
            Schema::create('connections', function ($table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->date('connected_at');
            });

            // Re-populate connections depuis interactions
            DB::table('interactions')
                ->where('type', 'connection')
                ->orderBy('id')
                ->chunk(100, function ($interactions) {
                    foreach ($interactions as $interaction) {
                        DB::table('connections')->insert([
                            'user_id'       => $interaction->user_id,
                            'connected_at'  => $interaction->interaction_at,
                        ]);
                    }
                });

            // Supprimer les interactions de type connection si nécessaire
            // DB::table('interactions')->where('type', 'connection')->delete();
        }
    }
};
