<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRubricsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rubrics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('title');
            $table->text('description');
            $table->string('icon')->nullable();
            $table->boolean('is_parent')->default(FALSE);
            $table->unsignedInteger('parent_id')->nullable()->index();
            $table->foreign('parent_id')->references('id')->on('rubrics');
            $table->enum('position', ['N', 'F']);
            $table->string('rank', 10);
            $table->boolean('contains_posts')->default(TRUE);
            $table->string('segment');
            $table->string('view', 31)->nullable();
            $table->timestamps();
        });

        Schema::create('group_rubric', function (Blueprint $table) {
            $table->unsignedInteger('group_id')->index();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->unsignedInteger('rubric_id')->index();
            $table->foreign('rubric_id')->references('id')->on('rubrics')->onDelete('cascade');
            $table->primary(['group_id', 'rubric_id']);
        });

        Schema::create('rubric_user', function (Blueprint $table) {
            $table->unsignedInteger('rubric_id')->index();
            $table->foreign('rubric_id')->references('id')->on('rubrics')->onDelete('cascade');
            $table->unsignedInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->primary(['rubric_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rubric_user');
        Schema::dropIfExists('group_rubric');
        Schema::dropIfExists('rubrics');
    }
}
