<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Suppression de la table rubric_user
        Schema::dropIfExists('rubric_user');

        // Suppression de la table favorites_apps
        Schema::dropIfExists('favorites_apps');

        // Suppression de la colonne is_favorite de la table post_user
        if (Schema::hasColumn('post_user', 'is_favorite')) {
            Schema::table('post_user', function (Blueprint $table) {
                $table->dropColumn('is_favorite');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // On ne recrée pas ces tables car les données ont été migrées.
        // Un rollback nécessiterait de restaurer le dump SQL.
    }
};
