<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_sso', 32)->nullable()->default(NULL);
            $table->string('name');
            $table->string('first_name');
            $table->string('avatar')->nullable()->default(NULL);
            $table->string('google_account')->unique()->nullable()->default(NULL);
            $table->boolean('is_staff')->default(0);
            $table->boolean('is_frozen')->default(0);
            $table->date('account_expires_on')->nullable()->default(NULL);
            $table->date('birthday')->nullable()->default(NULL);
            $table->enum('gender', ['F', 'M'])->nullable()->default(NULL);
            $table->string('language', 2)->default('FR');
            $table->string('status')->nullable()->default(NULL);
            $table->enum('quality', ['D', 'E', 'I'])->nullable()->default(NULL);
            $table->integer('code_ypareo')->unsigned()->nullable()->default(NULL);
            $table->integer('code_netypareo')->unsigned()->nullable()->default(NULL);
            $table->string('login')->nullable()->default(NULL);
            $table->string('ypareo_pwd')->nullable()->default(NULL);
            $table->integer('badge')->unsigned()->nullable()->default(NULL);
            $table->string('email')->unique()->nullable()->default(NULL);
            $table->timestamp('email_verified_at')->nullable()->default(NULL);
            $table->string('password')->nullable()->default(NULL);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
