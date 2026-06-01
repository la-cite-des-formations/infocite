<?php

namespace App\Http\Livewire;

use App\Models\Post;

/**
 * Trait pour la gestion des articles épinglés (mise à la Une).
 */
trait WithPinnedHandling
{
    /**
     * Nombre total d'articles épinglés.
     *
     * @var int
     */
    public $countPinnedPosts;

    /**
     * Met à jour le compteur d'articles épinglés.
     */
    public function countPinnedPosts(){

        $this->countPinnedPosts = Post::query()
            ->where('is_pinned',TRUE)
            ->count();
    }

    /**
     * Récupère la liste des articles épinglés.
     *
     * @return \Illuminate\Support\Collection Collection d'articles épinglés.
     */
    public function pinnedPosts()
    {
        $user = auth()->user();
        return Post::query()
            ->with(['rubric', 'author', 'comments', 'currentUserReader', 'gallery'])
            ->where('is_pinned', TRUE)
            ->whereIn('rubric_id', $user->myRubrics()->pluck('id'))
            ->get()
            ->filter(fn($post) => $user->can('read', $post));
    }

    /**
     * Alterne l'état épinglé d'un article.
     *
     * @param int $post_id ID de l'article.
     */
    public function switchPinnedPost($post_id) {
        $post = $this->post ?? Post::find($post_id);

        //on récupère le nombre d'articles épinglés
        $this->countPinnedPosts();

        if ($post->is_pinned) {

            Post::query()
                ->where('id', $post_id)
                ->update(['is_pinned' => FALSE]);

        }
        else {
            //Si le nombre d'articles épinglés est inférieur à 4, on peut épingler
            if( $this->countPinnedPosts < 4){
                Post::query()
                    ->where('id', $post_id)
                    ->update(['is_pinned' => TRUE]);
            }else{
                session()->flash('error_alert', 'Le nombre d\'articles épinglés ne doit pas excéder 4');
            }

        }
    }
}
