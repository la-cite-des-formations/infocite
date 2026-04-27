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
        Schema::create('favorites', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('favoriteable_id');
            $table->string('favoriteable_type');
            $table->integer('rank')->default(0);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->primary(['user_id', 'favoriteable_id', 'favoriteable_type'], 'favorites_primary');
            $table->index(['favoriteable_id', 'favoriteable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favorites');
    }
};
