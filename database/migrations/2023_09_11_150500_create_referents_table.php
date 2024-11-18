<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referents', function (Blueprint $table) {
            $table->unsignedInteger('id')->index();
            $table->primary('id');
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
            $table->string('label', 150);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referents');
    }
}
