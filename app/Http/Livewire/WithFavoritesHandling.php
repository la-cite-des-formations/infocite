<?php

namespace App\Http\Livewire;

use App\Models\Post;
use App\Models\Rubric;

trait WithFavoritesHandling
{
    public $isFavoriteRubric;
    public $isFavoritePost;

    public function switchFavoriteRubric($rubric_id = NULL) {
        $rubric = $rubric_id ? Rubric::find($rubric_id) : $this->rubric;

        if ($rubric->isFavorite()) {
            $rubric
                ->users()
                ->detach(auth()->user()->id);
        }
        else {
            $rubric
                ->users()
                ->attach(auth()->user()->id);
        }

        if (is_null($rubric_id)) {
            $this->isFavoriteRubric = !$this->isFavoriteRubric;
        }
        else {
            $this->loadUser();
        }
    }

    public function switchFavoritePost($post_id = NULL) {
        $post = $this->post ?? Post::find($post_id);

        if ($post->isFavorite()) {
            if ($post->isRead() || $post->tags()) {
                $this->isFavoritePost = FALSE;
            }
        }
        else {
            $this->isFavoritePost = TRUE;
        }

        if (isset($this->isFavoritePost)) {
            $post->readers()->syncWithoutDetaching([
                auth()->user()->id => [
                    'is_favorite' => $this->isFavoritePost
                ]
            ]);
        }
        else {
            $post->readers()->detach(auth()->user()->id);
        }
    }
}
