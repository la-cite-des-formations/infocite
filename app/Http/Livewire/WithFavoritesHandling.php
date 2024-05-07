<?php

namespace App\Http\Livewire;

use App\Post;

trait WithFavoritesHandling
{
    public $isFavoriteRubric;
    public $isFavoritePost;

    public function switchFavoriteRubric() {
        if ($this->rubric->isFavorite()) {
            $this->rubric
                ->users()
                ->detach(auth()->user()->id);
        }
        else {
            $this->rubric
                ->users()
                ->attach(auth()->user()->id);
        }

        $this->isFavoriteRubric = !$this->isFavoriteRubric;
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
