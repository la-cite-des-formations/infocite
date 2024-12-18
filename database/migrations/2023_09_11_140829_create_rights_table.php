<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rights', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->string('rd_role')->nullable()->default('NULL');
            $table->text('rd_description');
            $table->string('ed_role')->nullable()->default('NULL');
            $table->text('ed_description');
            $table->string('md_role')->nullable()->default('NULL');
            $table->text('md_description');
            $table->string('ad_role')->nullable()->default('NULL');
            $table->text('ad_description');
            $table->unsignedTinyInteger('default_roles')->default(0); // à changer en bit(4) dans la bdd
            $table->unsignedTinyInteger('dashboard_roles')->default(0); // à changer en bit(4) dans la bdd
            $table->timestamps();
        });

        Schema::create('rightables', function (Blueprint $table) {
            $table->unsignedInteger('right_id')->index();
            $table->foreign('right_id')->references('id')->on('rights')->onDelete('cascade');
            $table->unsignedInteger('rightable_id')->index();
            $table->string('rightable_type', 10);
            $table->unsignedInteger('resource_id')->index()->nullable()->default(NULL);
            $table->string('rightable_type', 10)->nullable()->default(NULL);
            $table->unsignedTinyInteger('proirity');
            $table->unsignedTinyInteger('roles'); // à changer en bit(4) dans la bdd
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rights');
    }
}
