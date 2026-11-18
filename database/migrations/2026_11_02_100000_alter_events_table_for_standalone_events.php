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
        Schema::table('events', function (Blueprint $table) {
            // Drop current cascade foreign key on post_id
            $table->dropForeign(['post_id']);

            // Make post_id nullable and set onDelete to set null
            $table->unsignedInteger('post_id')->nullable()->change();
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('set null');

            // Add standalone fields
            $table->string('title', 255)->after('id');
            $table->text('description')->after('title');
            $table->json('image')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['image', 'description', 'title']);

            $table->dropForeign(['post_id']);
            $table->unsignedInteger('post_id')->nullable(false)->change();
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }
};
