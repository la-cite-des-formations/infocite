<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('star_studients', function (Blueprint $table) {
            // On identifie toute les attributs dans la table Studients qui doivent être une clé étrangère
            // on indique leur attribut de référence dans l'autre table
            // ainsi que l'action à faire en cas de suppression
            $table->foreign('code_apprenant')->references('code_ypareo')->on('users')->onDelete('no action');
            $table->foreign('level_id')->references('id')->on('star_levels')->onDelete('no action');
            $table->foreign('training_id')->references('id')->on('star_trainings')->onDelete('no action');
        });
        echo "les clés de sutidents ont été ajouté.\n";
        Schema::table('star_trainings', function (Blueprint $table) {
            $table->foreign('sector_id')->references('id')->on('star_sectors')->onDelete('cascade');
            $table->foreign('degree_id')->references('id')->on('star_degrees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer la contrainte unique
            $table->dropUnique(['code_ypareo']);
        });
        Schema::table('star_trainings', function (Blueprint $table) {
            $table->dropForeign(['sector_id']);
            $table->dropForeign(['degree_id']);
        });
        Schema::table('star_studients', function (Blueprint $table) {
            $table->dropForeign(['code_apprenant']);
            $table->dropForeign(['level_id']);
            $table->dropForeign(['training_id']);
        });
    }
}
