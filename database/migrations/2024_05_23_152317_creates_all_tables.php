<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatesAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('star_studients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Clé étrangère vers users
            $table->integer('code_apprenant')->unique();
            $table->date('birthday');
            $table->enum('gender', ['F', 'M']);
            $table->unsignedBigInteger('level_id')->nullable();
            $table->unsignedBigInteger('trainning_id')->nullable();
            $table->string('adress');
            $table->string('city');
            $table->integer('postal_code');
            $table->string('family_situation');
            $table->string('language', 3);
            $table->enum('quality', ['D', 'E', 'I']);
            $table->string('status');
            $table->float('attendance');
            $table->timestamps();
        });echo "Table studients created.\n";
        Schema::create('star_trainnings', function (Blueprint $table) {
            $table->id();
            $table->integer('code_trainning')->unique();
            $table->string('name_trainning');
            $table->unsignedBigInteger('sector_id');
            $table->unsignedBigInteger('degree_id');
            $table->timestamps();
        });echo "Table trainnings created.\n";
        Schema::create('star_sectors', function (Blueprint $table) {
            $table->id();
            $table->integer('code_sector')->unique();
            $table->string('name');
            $table->timestamps();
        });echo "Table sectors created.\n";
        Schema::create('star_levels', function (Blueprint $table) {
            $table->id();
            $table->integer('code_level')->unique();
            $table->string('name');
            $table->timestamps();
        });echo "Table levels created.\n";
        Schema::create('star_degrees', function (Blueprint $table) {
            $table->id();
            $table->integer('code_degree')->unsigned();
            $table->string('name');
            $table->timestamps();
        });echo "Table degrees created.\n";


        Schema::table('star_studients', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('level_id')->references('id')->on('star_levels')->onDelete('set null');
            $table->foreign('trainning_id')->references('id')->on('star_trainnings')->onDelete('set null');
        });echo "les clés de sutidents ont été ajouté.\n";
        Schema::table('star_trainnings', function (Blueprint $table) {
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
        Schema::dropIfExists('star_studients');
        Schema::dropIfExists('star_trainnings');
        Schema::dropIfExists('star_sectors');
        Schema::dropIfExists('star_levels');
        Schema::dropIfExists('star_degrees');
        Schema::table('star_trainnings', function (Blueprint $table) {
            $table->dropForeign(['sector_id']);
            $table->dropForeign(['degree_id']);
        });

        Schema::table('star_studients', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['level_id']);
            $table->dropForeign(['trainning_id']);
        });
    }
}
