<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('icon');
            $table->string('title');
            $table->text('content');
            $table->boolean('published')->default(FALSE);
            $table->boolean('auto_delete')->default(FALSE);
            $table->boolean('is_pinned')->default(FALSE);
            $table->unsignedInteger('rubric_id')->index()->nullable()->default(NULL);
            $table->foreign('rubric_id')->references('id')->on('rubrics');
            $table->unsignedInteger('author_id')->index()->nullable()->default(NULL);
            $table->foreign('author_id')->references('id')->on('users');
            $table->unsignedInteger('corrector_id')->index()->nullable()->default(NULL);
            $table->foreign('corrector_id')->references('id')->on('users');
            $table->date('published_at')->nullable()->default(NULL);
            $table->date('expired_at')->nullable()->default(NULL);
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
        Schema::dropIfExists('posts');
    }
}
