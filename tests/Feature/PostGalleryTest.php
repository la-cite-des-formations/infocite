<?php

namespace Tests\Feature;

use App\Http\Livewire\Usage\PostGallery;
use App\Models\Gallery;
use App\Models\Post;
use App\Models\Rubric;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PostGalleryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        if (!\Illuminate\Support\Facades\Schema::hasTable('galleries')) {
            \Illuminate\Support\Facades\Schema::create('galleries', function ($table) {
                $table->increments('id');
                $table->unsignedInteger('post_id')->nullable();
                $table->json('images')->default('[]');
                $table->json('focal_point')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_auto_add_image_adds_to_temp_gallery_without_saving_to_database(): void
    {
        // 1. Simuler une image dans uploads/
        Storage::disk('public')->put('uploads/test-editor-image.jpg', 'fake-image-content');

        // 2. Initialiser le composant Livewire PostGallery
        $component = Livewire::test(PostGallery::class, ['postId' => null]);
        $sessionToken = session('post_gallery_token');

        $this->assertNotEmpty($sessionToken);
        $this->assertDatabaseCount('galleries', 0);

        // 3. Émettre autoAddImage
        $component->emit('autoAddImage', [
            'path' => '/storage/uploads/test-editor-image.jpg',
            'filename' => 'test-editor-image.jpg',
        ]);

        // 4. L'image doit être dans tempImages mais pas en base de données
        $this->assertDatabaseCount('galleries', 0);

        $tempImages = $component->get('tempImages');
        $this->assertCount(1, $tempImages);
        $this->assertEquals('image', $tempImages[0]['type']);
        $this->assertEquals('test-editor-image.jpg', $tempImages[0]['filename']);
        $this->assertEquals(1, $tempImages[0]['order']);
        $this->assertStringContainsString("temp_{$sessionToken}", $tempImages[0]['path']);

        // Vérifier que le fichier a bien été copié dans le dossier temporaire
        $copiedRelativePath = str_replace('/storage/', '', $tempImages[0]['path']);
        $this->assertTrue(Storage::disk('public')->exists($copiedRelativePath));
        // Et que le fichier original dans uploads/ existe toujours
        $this->assertTrue(Storage::disk('public')->exists('uploads/test-editor-image.jpg'));
    }

    public function test_add_youtube_video_to_gallery(): void
    {
        $component = Livewire::test(PostGallery::class, ['postId' => null]);

        $component->set('videoUrl', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ')
            ->call('addExternalVideo');

        $this->assertDatabaseCount('galleries', 0);

        $tempImages = $component->get('tempImages');
        $this->assertCount(1, $tempImages);
        $this->assertEquals('video', $tempImages[0]['type']);
        $this->assertEquals('youtube', $tempImages[0]['provider']);
        $this->assertEquals('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $tempImages[0]['path']);
        $this->assertNotEmpty($tempImages[0]['thumbnail']);
    }

    public function test_add_google_drive_video_to_gallery(): void
    {
        $component = Livewire::test(PostGallery::class, ['postId' => null]);

        $component->set('videoUrl', 'https://drive.google.com/file/d/1234567890abcdef/view')
            ->call('addExternalVideo');

        $tempImages = $component->get('tempImages');
        $this->assertCount(1, $tempImages);
        $this->assertEquals('video', $tempImages[0]['type']);
        $this->assertEquals('drive', $tempImages[0]['provider']);
        $this->assertEquals('https://drive.google.com/file/d/1234567890abcdef/preview', $tempImages[0]['path']);
        $this->assertNotEmpty($tempImages[0]['thumbnail']);
    }

    public function test_first_media_thumbnail_and_video_detection(): void
    {
        $gallery = new Gallery();
        $gallery->post_id = 999;
        $gallery->images = [
            [
                'type' => 'video',
                'provider' => 'youtube',
                'path' => 'https://www.youtube.com/watch?v=abc',
                'thumbnail' => 'https://img.youtube.com/vi/abc/hqdefault.jpg',
                'filename' => 'Vidéo YouTube',
                'order' => 1,
            ],
            [
                'type' => 'image',
                'path' => '/storage/galleries/999/photo.jpg',
                'filename' => 'photo.jpg',
                'order' => 2,
            ],
        ];

        $this->assertTrue($gallery->isFirstMediaVideo());
        $this->assertEquals('https://img.youtube.com/vi/abc/hqdefault.jpg', $gallery->firstThumbnail());
    }

    public function test_process_temp_gallery_moves_files_and_persists_in_database(): void
    {
        // 1. Simuler une image dans uploads/
        Storage::disk('public')->put('uploads/photo.jpg', 'img-data');

        // 2. Initialiser PostGallery et ajouter l'image
        $component = Livewire::test(PostGallery::class, ['postId' => null]);
        $sessionToken = session('post_gallery_token');

        $component->emit('autoAddImage', [
            'path' => '/storage/uploads/photo.jpg',
            'filename' => 'photo.jpg',
        ]);

        $this->assertDatabaseCount('galleries', 0);

        // 3. Exécuter le processus de persistance appelé par EditPostManager lors de l'enregistrement de l'article
        PostGallery::processTempGallery(42, $sessionToken);

        // 4. La galerie doit désormais exister en BDD pour le post 42
        $this->assertDatabaseCount('galleries', 1);

        $savedGallery = Gallery::where('post_id', 42)->first();
        $this->assertNotNull($savedGallery);
        $this->assertCount(1, $savedGallery->images);
        $this->assertStringContainsString('galleries/42/', $savedGallery->images[0]['path']);

        // Le fichier doit être déplacé dans galleries/42/
        $finalStoragePath = str_replace('/storage/', '', $savedGallery->images[0]['path']);
        $this->assertTrue(Storage::disk('public')->exists($finalStoragePath));
    }

    public function test_auto_add_video_from_editor(): void
    {
        $component = Livewire::test(PostGallery::class, ['postId' => null]);

        // 1. Émettre autoAddVideo avec une URL YouTube (telle qu'extraite d'un iframe ou d'un lien TinyMCE)
        $component->emit('autoAddVideo', 'https://www.youtube.com/embed/dQw4w9WgXcQ');

        $tempImages = $component->get('tempImages');
        $this->assertCount(1, $tempImages);
        $this->assertEquals('video', $tempImages[0]['type']);
        $this->assertEquals('youtube', $tempImages[0]['provider']);
        $this->assertEquals('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $tempImages[0]['path']);

        // 2. Émettre la même vidéo à nouveau (anti-doublon)
        $component->emit('autoAddVideo', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        $this->assertCount(1, $component->get('tempImages'));

        // 3. Émettre une vidéo Google Drive
        $component->emit('autoAddVideo', 'https://drive.google.com/file/d/test12345/preview');
        $this->assertCount(2, $component->get('tempImages'));
        $this->assertEquals('drive', $component->get('tempImages')[1]['provider']);
    }
}
