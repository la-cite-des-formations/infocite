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

    public function switchFavoritePost($post_id) {
        $post = Post::find($post_id);

        if ($post->isFavorite()) {
            if ($post->isRead() || $post->tags()) {
                $isFavorite = FALSE;
            }
        }
        else {
            $isFavorite = TRUE;
        }
        if (isset($isFavorite)) {
            $post->readers()->syncWithoutDetaching([
                auth()->user()->id => [
                    'is_favorite' => $isFavorite
                ]
            ]);
        }
        else {
            $post->readers()->detach(auth()->user()->id);
        }
        $this->emitSelf('render');
    }
}
