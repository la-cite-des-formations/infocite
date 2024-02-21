<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apps', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owner_id')->unsigned()->index();
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('auth_type', ['N', 'G', 'S']);
            $table->string('name');
            $table->text('description')->nullable()->default(NULL);
            $table->json('features')->nullable()->default(NULL);
            $table->string('favicon')->nullable()->default(NULL);
            $table->string('icon', 20)->nullable()->default(NULL);
            $table->string('url');
            $table->timestamps();
        });

        Schema::create('app_user', function (Blueprint $table) {
            $table->integer('app_id')->unsigned()->index();
            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->primary(['app_id', 'user_id']);
            $table->string('login')->nullable()->default(NULL);
            $table->string('password')->nullable()->default(NULL);
        });

        Schema::create('app_group', function (Blueprint $table) {
            $table->integer('app_id')->unsigned()->index();
            $table->foreign('app_id')->references('id')->on('apps')->onDelete('cascade');
            $table->integer('group_id')->unsigned()->index();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->primary(['app_id', 'group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_group');
        Schema::dropIfExists('app_user');
        Schema::dropIfExists('apps');
    }
}
