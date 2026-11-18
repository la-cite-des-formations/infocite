<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventType;
use App\Models\Post;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AgendaEventsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('event_types')) {
            Schema::create('event_types', function ($table) {
                $table->increments('id');
                $table->string('name', 100);
                $table->string('color', 7);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('rubrics')) {
            Schema::create('rubrics', function ($table) {
                $table->increments('id');
                $table->string('name');
                $table->string('title')->nullable();
                $table->string('segment')->unique();
                $table->unsignedInteger('parent_id')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function ($table) {
                $table->increments('id');
                $table->unsignedInteger('rubric_id')->nullable();
                $table->string('title');
                $table->text('content')->nullable();
                $table->boolean('published')->default(true);
                $table->boolean('auto_delete')->default(false);
                $table->boolean('is_template')->default(false);
                $table->boolean('is_pinned')->default(false);
                $table->boolean('is_acknowledgment_required')->default(false);
                $table->boolean('is_rating_enabled')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('events')) {
            Schema::create('events', function ($table) {
                $table->increments('id');
                $table->string('title', 255);
                $table->text('description');
                $table->json('image')->nullable();
                $table->unsignedInteger('post_id')->nullable();
                $table->unsignedInteger('event_type_id');
                $table->date('start_date');
                $table->time('start_time')->nullable();
                $table->date('end_date')->nullable();
                $table->time('end_time')->nullable();
                $table->string('location', 150)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('galleries')) {
            Schema::create('galleries', function ($table) {
                $table->increments('id');
                $table->unsignedInteger('post_id');
                $table->json('images')->nullable();
                $table->timestamps();
            });
        }

        EventType::firstOrCreate(['id' => 1], ['name' => 'Séminaire', 'color' => '#27ae60']);
    }

    /**
     * Teste la création d'un événement autonome dans l'agenda.
     */
    public function test_standalone_event_creation_and_filtering()
    {
        $type = EventType::first();

        $event = Event::create([
            'title' => 'Salon des métiers 2026',
            'description' => 'Présentation des filières apprentissage.',
            'event_type_id' => $type->id,
            'start_date' => '2026-10-15',
            'start_time' => '09:00',
            'end_date' => '2026-10-16',
            'end_time' => '17:00',
            'location' => 'Parc des expositions',
            'image' => [
                'path' => '/storage/events/salon.jpg',
                'focal_point' => ['x' => 40.0, 'y' => 50.0]
            ],
        ]);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Salon des métiers 2026',
            'post_id' => null,
        ]);

        $this->assertEquals('Salon des métiers 2026', $event->title);
        $this->assertEquals(['x' => 40.0, 'y' => 50.0], $event->getFocalPoint());

        // Test filtrage
        $filtered = Event::filter(['search' => 'Salon des métiers'])->get();
        $this->assertTrue($filtered->contains('id', $event->id));

        $filteredByType = Event::filter(['event_type_id' => $type->id])->get();
        $this->assertTrue($filteredByType->contains('id', $event->id));
    }

    /**
     * Teste la liaison optionnelle à un article et le détachement lors de la suppression de l'article.
     */
    public function test_event_linked_to_post_and_detachment()
    {
        $post = Post::create(['title' => 'Article Journée Métiers', 'content' => 'Contenu test']);
        $type = EventType::first();

        $event = Event::create([
            'title' => $post->title,
            'description' => 'Description liée',
            'event_type_id' => $type->id,
            'post_id' => $post->id,
            'start_date' => '2026-11-01',
        ]);

        $this->assertNotNull($event->post);
        $this->assertEquals($post->id, $event->post_id);

        // Détachement
        $event->update(['post_id' => null]);
        $event->refresh();

        $this->assertNull($event->post_id);
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'post_id' => null,
        ]);
    }

    /**
     * Teste le rendu de la modale de détail pour un événement autonome et un événement lié.
     */
    public function test_event_detail_modal_loads()
    {
        $type = EventType::first();

        // Événement autonome
        $standaloneEvent = Event::create([
            'title' => 'Événement autonome modale',
            'description' => 'Description test sans article',
            'event_type_id' => $type->id,
            'start_date' => '2026-11-20',
        ]);

        \Livewire\Livewire::test(\App\Http\Livewire\Modals\Usage\EventDetail::class, ['data' => ['eventId' => $standaloneEvent->id]])
            ->assertSee('Événement autonome modale')
            ->assertSee('Description test sans article')
            ->assertDontSee('Consulter');

        // Événement lié
        $rubric = \App\Models\Rubric::firstOrCreate(['segment' => 'actualites'], ['name' => 'Actualités']);
        $post = Post::create(['title' => 'Article lié modale', 'content' => 'Contenu test', 'rubric_id' => $rubric->id]);
        $linkedEvent = Event::create([
            'title' => 'Événement lié modale',
            'description' => 'Description test avec article',
            'event_type_id' => $type->id,
            'post_id' => $post->id,
            'start_date' => '2026-11-25',
        ]);

        \Livewire\Livewire::test(\App\Http\Livewire\Modals\Usage\EventDetail::class, ['data' => ['eventId' => $linkedEvent->id]])
            ->assertSee('Événement lié modale')
            ->assertSee('Description test avec article')
            ->assertSee('Consulter');
    }

    /**
     * Teste l'importation d'événements via CSV (format ISO, format FR, avec BOM).
     */
    public function test_csv_import_handles_formats_and_bom()
    {
        $type = EventType::first();

        // Créer un fichier CSV temporaire avec BOM, format ISO et format FR
        $csvContent = "\xEF\xBB\xBF" .
            "titre,description,type_evenement,date_debut,heure_debut,date_fin,heure_fin,lieu,id_article\n" .
            "Événement ISO,Description ISO,{$type->name},2026-12-01,09:00,2026-12-01,17:00,Salle A,\n" .
            "Événement FR,Description FR,{$type->id},15/12/2026,14:00,15/12/2026,18:00,Salle B,\n" .
            ",Ligne sans titre,{$type->name},2026-12-05,,,,,\n"; // Ligne invalide

        $uploadedFile = \Illuminate\Http\UploadedFile::fake()->createWithContent(
            'import_test.csv',
            $csvContent
        );

        $component = \Livewire\Livewire::test(\App\Http\Livewire\Modals\Usage\ImportEventsCsv::class)
            ->set('csvFile', $uploadedFile)
            ->call('import');

        $component->assertSet('importedCount', 2)
            ->assertSet('processedTotal', 3);

        $this->assertDatabaseHas('events', [
            'title' => 'Événement ISO',
            'location' => 'Salle A',
        ]);

        $this->assertDatabaseHas('events', [
            'title' => 'Événement FR',
            'location' => 'Salle B',
        ]);
    }

    /**
     * Teste le composant d'administration EventsManager et la suppression d'événements.
     */
    public function test_admin_events_manager_and_deletion()
    {
        $type = EventType::first();

        $event1 = Event::create([
            'title' => 'Admin Event Alpha',
            'description' => 'Test admin desc',
            'event_type_id' => $type->id,
            'start_date' => '2026-11-10',
        ]);

        $event2 = Event::create([
            'title' => 'Admin Event Beta',
            'description' => 'Test admin desc 2',
            'event_type_id' => $type->id,
            'start_date' => '2026-11-12',
        ]);

        // Vérifier l'affichage et le filtre dans EventsManager
        \Livewire\Livewire::test(\App\Http\Livewire\Admin\EventsManager::class)
            ->assertSee('Admin Event Alpha')
            ->assertSee('Admin Event Beta')
            ->set('filter.search', 'Alpha')
            ->assertSee('Admin Event Alpha')
            ->assertDontSee('Admin Event Beta');

        // Tester la modale de suppression Admin Delete
        \Livewire\Livewire::test(\App\Http\Livewire\Modals\Admin\Events\Delete::class, ['eventsIDs' => [$event1->id]])
            ->assertSee('Admin Event Alpha')
            ->call('delete')
            ->assertSet('deletionPerformed', true);

        $this->assertDatabaseMissing('events', ['id' => $event1->id]);
        $this->assertDatabaseHas('events', ['id' => $event2->id]);
    }

    /**
     * Teste la substitution du titre et de la description depuis l'article lié dans l'import CSV.
     */
    public function test_csv_import_with_post_id_substitution()
    {
        $type = EventType::first();
        $post = Post::create([
            'title' => 'Article pour Import Test',
            'content' => '<p>Contenu détaillé de l\'article pour test.</p>',
        ]);

        $csvContent = "\xEF\xBB\xBF" .
            "titre,description,type_evenement,date_debut,heure_debut,date_fin,heure_fin,lieu,id_article\n" .
            ",,{$type->id},2026-12-20,10:00,2026-12-20,12:00,Salle C,{$post->id}\n";

        $uploadedFile = \Illuminate\Http\UploadedFile::fake()->createWithContent(
            'import_subst_test.csv',
            $csvContent
        );

        $component = \Livewire\Livewire::test(\App\Http\Livewire\Modals\Usage\ImportEventsCsv::class)
            ->set('csvFile', $uploadedFile)
            ->call('import');

        $component->assertSet('importedCount', 1);

        $this->assertDatabaseHas('events', [
            'title' => 'Article pour Import Test',
            'description' => 'Contenu détaillé de l\'article pour test.',
            'post_id' => $post->id,
            'location' => 'Salle C',
        ]);
    }

    /**
     * Teste la modale admin d'édition et de visualisation d'événements.
     */
    public function test_admin_events_edit_modal()
    {
        $type = EventType::first();

        // 1. Création via la modale admin
        \Livewire\Livewire::test(\App\Http\Livewire\Modals\Admin\Events\Edit::class, ['data' => ['mode' => 'creation']])
            ->set('title', 'Nouvel événement Admin')
            ->set('description', 'Description créée depuis admin')
            ->set('event_type_id', $type->id)
            ->set('start_date', '2026-12-25')
            ->call('save');

        $createdEvent = Event::where('title', 'Nouvel événement Admin')->first();
        $this->assertNotNull($createdEvent);
        $this->assertEquals('Description créée depuis admin', $createdEvent->description);

        // 2. Visualisation (mode view)
        \Livewire\Livewire::test(\App\Http\Livewire\Modals\Admin\Events\Edit::class, ['data' => ['mode' => 'view', 'id' => $createdEvent->id]])
            ->assertSee('Nouvel événement Admin')
            ->assertSee('Description créée depuis admin');

        // 3. Modification (mode edition)
        \Livewire\Livewire::test(\App\Http\Livewire\Modals\Admin\Events\Edit::class, ['data' => ['mode' => 'edition', 'id' => $createdEvent->id]])
            ->set('title', 'Événement Admin Modifié')
            ->call('save');

        $this->assertDatabaseHas('events', [
            'id' => $createdEvent->id,
            'title' => 'Événement Admin Modifié',
        ]);
    }

    /**
     * Teste que la modification d'un article ne modifie PAS le titre et la description de l'événement associé.
     */
    /**
     * Teste que la modification d'un article ne modifie PAS le titre et la description de l'événement associé.
     */
    public function test_post_update_preserves_event_title_and_description()
    {
        $type = EventType::first();

        // 1. Création d'un article avec événement
        $post = Post::create([
            'title' => 'Titre Initial Article',
            'content' => '<p>Contenu Initial Article</p>',
        ]);

        $event = Event::create([
            'title' => 'Titre Personnalisé Événement',
            'description' => 'Description Personnalisée Événement',
            'event_type_id' => $type->id,
            'post_id' => $post->id,
            'start_date' => '2026-11-20',
        ]);

        // Simuler la logique de mise à jour en mode édition (EditPostManager et Admin/Posts/Edit)
        $eventData = [
            'event_type_id' => $type->id,
            'start_date' => '2026-11-21',
            'start_time' => null,
            'end_date' => null,
            'end_time' => null,
            'location' => null,
        ];

        $mode = 'edition';
        $existingEvent = $post->event;
        if ($mode === 'creation' || !$existingEvent) {
            $eventData['title'] = $post->title;
            $eventData['description'] = strip_tags($post->content) ?: $post->title;
        }

        $post->event()->updateOrCreate([], $eventData);

        $event->refresh();

        // La date doit être mise à jour
        $this->assertEquals('2026-11-21', $event->start_date->format('Y-m-d'));

        // Mais le titre et la description NE doivent PAS être écrasés
        $this->assertEquals('Titre Personnalisé Événement', $event->title);
        $this->assertEquals('Description Personnalisée Événement', $event->description);
    }
}
