<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('group_id')->index();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->unsignedInteger('parent_id')->index()->nullable()->default(NULL);
            $table->foreign('parent_id')->references('id')->on('processes')->onDelete('cascade');
            $table->unsignedInteger('manager_id')->index()->nullable()->default(NULL);
            $table->foreign('manager_id')->references('id')->on('actors')->onDelete('cascade');
            $table->unsignedInteger('format_id')->index();
            $table->foreign('format_id')->references('id')->on('formats')->onDelete('cascade');
            $table->string('rank', 20)->default('-');
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
        Schema::dropIfExists('processes');
    }
}
