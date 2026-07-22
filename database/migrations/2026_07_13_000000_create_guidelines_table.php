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
        Schema::create('guidelines', function (Blueprint $table) {
            $table->increments('id');

            // Lien vers l'article du guide en ligne
            $table->unsignedInteger('post_id')->index();
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');

            // Identifiant unique du contexte d'interface (ex: 'desktop-notifications')
            $table->string('context_key')->unique();

            // Clé du contexte de l'étape suivante pour les parcours guidés
            $table->string('next_context_key')->nullable()->default(null);

            // Ouverture automatique à la première visite (onboarding)
            $table->boolean('auto_open')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guidelines');
    }
};
