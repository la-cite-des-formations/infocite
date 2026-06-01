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
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('is_rating_enabled')->after('is_acknowledgment_required')->default(false);
        });

        Schema::table('post_user', function (Blueprint $table) {
            $table->tinyInteger('rating')->after('tags')->default(0)->comment('Note de 1 à 5 (0 = non noté)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('is_rating_enabled');
        });

        Schema::table('post_user', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
    }
};
