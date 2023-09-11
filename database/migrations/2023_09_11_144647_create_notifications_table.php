<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('post_id')->index()->nullable()->default(NULL);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->enum('content_type', ['NP', 'UP', 'CP', 'NA', 'UA', 'UO'])->nullable()->default(NULL);
            $table->date('release_at')->nullable()->default(NULL);
            $table->timestamps();
        });

        Schema::create('notification_user', function (Blueprint $table) {
            $table->unsignedInteger('notification_id')->index();
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->primary(['notification_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_user');
        Schema::dropIfExists('notifications');
    }
}
