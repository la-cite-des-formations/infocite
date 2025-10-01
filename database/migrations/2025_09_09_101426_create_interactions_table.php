<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();

            // Utilisateur interagissant
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // polymorphique nullable : cible de l'interaction
            $table->string('target_type')->nullable()->default(NULL);
            $table->unsignedInteger('target_id')->nullable()->default(NULL);

            // type d'action
            $table->string('type', 50)->comment("Type de l'action");

            // date de l'action
            $table->date('interaction_at')->default(DB::raw('CURRENT_DATE'));

            // indexes pour stats
            $table->index(['user_id', 'interaction_at']);
            $table->index(['target_type', 'target_id', 'interaction_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interactions');
    }
};
