<?php

use Illuminate\Database\Migrations\Migration;
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
        // Récupérer la rubrique Guide en ligne
        $rubricId = DB::table('rubrics')->where('segment', 'guide-en-ligne')->value('id');

        if (!$rubricId) {
            return;
        }

        // Récupérer un auteur (admin de préférence)
        $authorId = 97;

        // 1. Premier article : Guide d'accueil et recherche (déclenché automatiquement sur 'une')
        $postContent1 = <<<'HTML'
<p>Bienvenue sur votre portail Info-Cité !</p>
<p>Vous pouvez rechercher n'importe quel contenu sur l'intranet grâce à la barre de recherche globale mise en évidence en bas de la page.</p>
<p>Saisissez vos mots-clés et appuyez sur Entrée pour trouver instantanément des articles ou applications.</p>
HTML;

        $postId1 = DB::table('posts')->insertGetId([
            'icon'          => 'search',
            'title'         => 'Rechercher sur Info-Cité',
            'content'       => $postContent1,
            'published'     => true,
            'rubric_id'     => $rubricId,
            'author_id'     => $authorId,
            'corrector_id'  => null,
            'published_at'  => now()->toDateString(),
            'expired_at'    => null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('guidelines')->insert([
            'post_id'           => $postId1,
            'context_key'       => 'une',
            'next_context_key'  => 'guide-applis',
            'auto_open'         => true,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        // 2. Deuxième article : Vos applications (étape suivante)
        $postContent2 = <<<'HTML'
<p>Accédez rapidement à tous vos outils quotidiens via la section "Mes applis".</p>
<p>Vous pouvez y marquer vos applications favorites pour les retrouver en un clic dès votre arrivée sur le portail.</p>
HTML;

        $postId2 = DB::table('posts')->insertGetId([
            'icon'          => 'apps',
            'title'         => 'Vos applications favorites',
            'content'       => $postContent2,
            'published'     => true,
            'rubric_id'     => $rubricId,
            'author_id'     => $authorId,
            'corrector_id'  => null,
            'published_at'  => now()->toDateString(),
            'expired_at'    => null,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('guidelines')->insert([
            'post_id'           => $postId2,
            'context_key'       => 'guide-applis',
            'next_context_key'  => null,
            'auto_open'         => false,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $postIds = DB::table('guidelines')
            ->whereIn('context_key', ['une', 'guide-applis'])
            ->pluck('post_id')
            ->toArray();

        DB::table('guidelines')->whereIn('context_key', ['une', 'guide-applis'])->delete();
        DB::table('posts')->whereIn('id', $postIds)->delete();
    }
};
