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
        Schema::create('favorites_apps', function (Blueprint $table) {
            // Champs de la table
            $table->unsignedInteger('app_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('rank')->nullable();

            // Clé primaire
            $table->primary(['app_id', 'user_id']);

            // Clés étrangères
            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favorites_apps');
    }
};
