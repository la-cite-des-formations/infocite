<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Crée la rubrique "Guide en ligne" et insère le premier article de guide
     * (notifications de bureau) avec son entrée dans la table guidelines.
     *
     * @return void
     */
    public function up()
    {
        // 1. Création de la rubrique parente "Aide" (si elle n'existe pas)
        $parentRubricId = DB::table('rubrics')->where('segment', 'aide')->value('id');

        if (!$parentRubricId) {
            $parentRubricId = DB::table('rubrics')->insertGetId([
                'name'          => 'Aide',
                'title'         => 'Aide',
                'description'   => 'Ressources d\'aide et de documentation en ligne.',
                'icon'          => 'help_center',
                'is_parent'     => true,
                'parent_id'     => null,
                'position'      => 'U',
                'rank'          => '90',
                'contains_posts'=> false,
                'segment'       => 'aide',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        // 2. Création de la rubrique enfant "Guide en ligne"
        $rubricId = DB::table('rubrics')->where('segment', 'guide-en-ligne')->value('id');

        if (!$rubricId) {
            $rubricId = DB::table('rubrics')->insertGetId([
                'name'          => 'Guide en ligne',
                'title'         => 'Guide en ligne',
                'description'   => 'Articles d\'aide contextuelle et guides d\'utilisation d\'Info-Cité.',
                'icon'          => 'menu_book',
                'is_parent'     => false,
                'parent_id'     => $parentRubricId,
                'position'      => 'U',
                'rank'          => '91',
                'contains_posts'=> true,
                'segment'       => 'guide-en-ligne',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        // 3. Insertion de l'article "Notifications de bureau"
        $adminUser = DB::table('users')->where('is_admin', true)->orWhereRaw("roles & 4")->first();
        $authorId = $adminUser?->id ?? DB::table('users')->first()?->id;

        $postContent = <<<'HTML'
<p>À la fermeture de ce message, une pop-up vous invitant à afficher les notifications apparaîtra. SVP, autorisez-les.</p>

<div class="alert alert-warning" role="alert">
    <p>
        <strong>Important !</strong> Si vous les bloquez au niveau de votre navigateur, vous ne pourrez plus les activer via Info-Cité.
        Pour ce faire, vous devrez impérativement les autoriser au niveau du navigateur.
    </p>
    <h6>Autoriser manuellement les notifications avec Chrome</h6>
    <video src="/img/unblock_notifications_guide.mp4" controls width="400" type="video/mp4" poster="/img/unblock_notifications_poster.png">
        Débloquer les notifications sur Chrome
    </video>
</div>

<p>Via la page 'Mes infos' d'Info-Cité (bouton à droite du menu de navigation), vous aurez la possibilité de les refuser ultérieurement, de les accepter toutes ou en partie suivant vos favoris.</p>
<p>L'avantage ! être notifié même si l'application est réduite, fermée ou bien dans un onglet inactif. Désormais, ne manquez plus aucune info...</p>

<div class="alert alert-danger" role="alert">
    <p>
        <strong>Attention !</strong> Ignorer la pop-up 2 à 3 fois de suite revient à bloquer les notifications.
    </p>
</div>
HTML;

        $existingPostId = DB::table('guidelines')->where('context_key', 'desktop-notifications')->value('post_id');

        if (!$existingPostId) {
            $postId = DB::table('posts')->insertGetId([
                'icon'          => 'notifications_active',
                'title'         => 'Notifications de bureau',
                'content'       => $postContent,
                'published'     => true,
                'rubric_id'     => $rubricId,
                'author_id'     => $authorId,
                'corrector_id'  => null,
                'published_at'  => now()->toDateString(),
                'expired_at'    => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // 4. Insertion de l'entrée de liaison dans la table guidelines
            DB::table('guidelines')->insert([
                'post_id'           => $postId,
                'context_key'       => 'desktop-notifications',
                'css_selector'      => null,
                'next_context_key'  => null,
                'auto_open'         => true,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Suppression de l'entrée guideline et de l'article associé
        $postId = DB::table('guidelines')->where('context_key', 'desktop-notifications')->value('post_id');
        DB::table('guidelines')->where('context_key', 'desktop-notifications')->delete();
        if ($postId) {
            DB::table('posts')->where('id', $postId)->delete();
        }

        // Suppression des rubriques si vides
        $rubricId = DB::table('rubrics')->where('segment', 'guide-en-ligne')->value('id');
        if ($rubricId && DB::table('posts')->where('rubric_id', $rubricId)->doesntExist()) {
            DB::table('rubrics')->where('id', $rubricId)->delete();
        }
    }
};
