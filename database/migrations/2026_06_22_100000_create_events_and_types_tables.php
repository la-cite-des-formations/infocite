<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('color', 7); // Code hexadécimal (ex: #E67E22)
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('post_id')->index();
            $table->unsignedInteger('event_type_id')->index();
            $table->date('start_date');
            $table->time('start_time')->nullable();
            $table->date('end_date')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location', 150)->nullable();
            $table->timestamps();

            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('event_type_id')->references('id')->on('event_types')->onDelete('cascade');
        });

        // Insert initial 12 event types
        $initialTypes = [
            ['name' => 'Événement interne', 'color' => '#3498db'],
            ['name' => 'Événement externe', 'color' => '#2ecc71'],
            ['name' => 'Concours', 'color' => '#e74c3c'],
            ['name' => 'Salon & forums', 'color' => '#9b59b6'],
            ['name' => 'Conférence', 'color' => '#1abc9c'],
            ['name' => 'Portes ouvertes', 'color' => '#f1c40f'],
            ['name' => 'Remise des diplômes', 'color' => '#34495e'],
            ['name' => 'Jobs dating / Info', 'color' => '#e67e22'],
            ['name' => 'Afterworks', 'color' => '#d35400'],
            ['name' => 'Séminaire', 'color' => '#27ae60'],
            ['name' => 'Réunions institutionnelles', 'color' => '#7f8c8d'],
            ['name' => 'Rencontres professionnelles', 'color' => '#8e44ad'],
        ];

        foreach ($initialTypes as $type) {
            DB::table('event_types')->insert(array_merge($type, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create Agenda child rubric under 'Evénements' (ID 15)
        $parentRubric = DB::table('rubrics')->where('id', 15)->first();
        if ($parentRubric) {
            $agendaId = DB::table('rubrics')->insertGetId([
                'name' => 'Agenda',
                'title' => 'Agenda',
                'description' => 'Agenda des événements à venir',
                'icon' => 'timeline',
                'is_parent' => 0,
                'parent_id' => 15,
                'position' => 'N',
                'rank' => '050-005',
                'contains_posts' => 0,
                'segment' => 'agenda',
                'view' => 'agenda',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Sync groups from parent to child
            $parentGroupIds = DB::table('group_rubric')
                ->where('rubric_id', 15)
                ->pluck('group_id');

            foreach ($parentGroupIds as $groupId) {
                DB::table('group_rubric')->insert([
                    'group_id' => $groupId,
                    'rubric_id' => $agendaId,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Delete Agenda rubric if it exists
        $agendaRubric = DB::table('rubrics')
            ->where('parent_id', 15)
            ->where('segment', 'agenda')
            ->first();

        if ($agendaRubric) {
            DB::table('group_rubric')
                ->where('rubric_id', $agendaRubric->id)
                ->delete();
            DB::table('rubrics')
                ->where('id', $agendaRubric->id)
                ->delete();
        }

        Schema::dropIfExists('events');
        Schema::dropIfExists('event_types');
    }
};
