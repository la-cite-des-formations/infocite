<?php

namespace App\Http\Livewire;

use App\Models\Post;
use App\Models\Rubric;

/**
 * Trait pour la gestion des mises en favoris des rubriques et des articles.
 */
trait WithFavoritesHandling
{
    /**
     * État de favori pour la rubrique.
     *
     * @var bool
     */
    public $isFavoriteRubric;

    /**
     * État de favori pour l'article.
     *
     * @var bool
     */
    public $isFavoritePost;

    /**
     * Alterne l'état de favori pour une rubrique.
     *
     * @param int|null $rubric_id ID de la rubrique (si null, utilise la rubrique chargée dans le composant).
     */
    /**
     * Alterne l'état de favori pour une rubrique.
     *
     * @param int|null $rubric_id ID de la rubrique (si null, utilise la rubrique chargée dans le composant).
     */
    public function switchFavoriteRubric($rubric_id = NULL) {
        $rubric = $rubric_id ? Rubric::find($rubric_id) : $this->rubric;

        $newState = $rubric->toggleFavorite();

        if (is_null($rubric_id)) {
            $this->isFavoriteRubric = $newState;
        }
        else {
            $this->loadUser();
        }
    }

    /**
     * Alterne l'état de favori pour un article.
     *
     * @param int|null $post_id ID de l'article (si null, utilise l'article chargé dans le composant).
     */
    public function switchFavoritePost($post_id = NULL) {
        $post = $this->post ?? Post::find($post_id);

        $this->isFavoritePost = $post->toggleFavorite();
    }
}
