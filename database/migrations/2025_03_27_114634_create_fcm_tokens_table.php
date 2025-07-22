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
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255)->unique();
            $table->string('computer_id', 20)->nullable();
            $table->string('browser', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('fcm_token_user', function (Blueprint $table) {
            //Champs de la table
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('fcm_token_id');

            // Clé primaire
            $table->primary(['user_id', 'fcm_token_id']);

            // Clés étrangères
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('fcm_token_id')->references('id')->on('fcm_tokens')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            // Champs de la table supplémentaires
            $table->boolean('desktop_notifications_granted')->default(1)->after('is_staff');
            $table->boolean('notify_only_favorites')->default(0)->after('desktop_notifications_granted');
            $table->unsignedInteger('default_fcm_token_id')->nullable()->after('remember_token');

            // Nouvelle clé étrangère
            $table->foreign('default_fcm_token_id')->references('id')->on('fcm_tokens')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fcm_token_user');
        Schema::dropIfExists('fcm_tokens');
    }
};
