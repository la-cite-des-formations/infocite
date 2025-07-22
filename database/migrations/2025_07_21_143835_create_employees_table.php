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
        Schema::create('employees', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('desktop_notifications_granted')->default(TRUE);
            $table->boolean('notify_only_favorites')->default(FALSE);
            $table->unsignedInteger('default_fcm_token_id')->nullable();
            $table->foreign('default_fcm_token_id')->references('id')->on('fcm_tokens')->onDelete('set null');
            $table->string('position')->nullable();
            $table->string('building')->nullable();
            $table->string('office')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('employees');
    }
};
