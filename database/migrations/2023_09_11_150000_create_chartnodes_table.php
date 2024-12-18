<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChartnodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chartnodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->Integer('code_fonction')->nullable()->default(NULL);
            $table->unsignedInteger('parent_id')->index()->nullable()->default(NULL);
            $table->foreign('parent_id')->references('id')->on('chartnodes')->onDelete('set null');
            $table->unsignedInteger('format_id')->index()->nullable()->default(NULL);
            $table->foreign('format_id')->references('id')->on('formats')->onDelete('set null');
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
        Schema::dropIfExists('chartnodes');
    }
}
