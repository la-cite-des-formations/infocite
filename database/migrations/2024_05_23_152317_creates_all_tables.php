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
            $table->increments('id');
            $table->integer('code_apprenant')->unsigned();// Clé étrangère vers users
            $table->date('birthday');
            $table->enum('gender', ['F', 'M']);
            $table->integer('level_id')->nullable()->unsigned();
            $table->integer('training_id')->nullable()->unsigned();
            $table->string('adress')->nullable();
            $table->string('city')->nullable();
            $table->integer('postal_code')->nullable();
            $table->string('family_situation')->nullable();
            $table->string('language', 3);
            $table->enum('quality', ['D', 'E', 'I'])->nullable();
            $table->string('status')->nullable();
            $table->float('attendance')->nullable();
            $table->timestamps();
        });echo "Table studients created.\n";
        
        Schema::create('star_trainings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('code_training')->unsigned();
            $table->string('name_training');
            $table->integer('sector_id')->unsigned();
            $table->integer('degree_id')->unsigned();
            $table->timestamps();
        });echo "Table trainings created.\n";
        
        Schema::create('star_sectors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('code_sector')->unsigned();
            $table->string('name');
            $table->timestamps();
        });echo "Table sectors created.\n";
        
        Schema::create('star_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('code_level')->unsigned();
            $table->string('name');
            $table->timestamps();
        });echo "Table levels created.\n";
        
        Schema::create('star_degrees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('code_degree')->unsigned();
            $table->string('name');
            $table->timestamps();
        });echo "Table degrees created.\n";
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('star_studients');
        Schema::dropIfExists('star_trainings');
        Schema::dropIfExists('star_sectors');
        Schema::dropIfExists('star_levels');
        Schema::dropIfExists('star_degrees');
    }
}
