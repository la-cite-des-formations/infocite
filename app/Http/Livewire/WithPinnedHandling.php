<?php

namespace App\Http\Livewire;

use App\Post;

trait WithPinnedHandling
{
    public $isPinned;

    public function switchPinnedPost($post_id = NULL) {
        $post = $this->post ?? Post::find($post_id);

        //On récupère le nombre de poste épinglé dans la table post_user
        $countPinnedPosts = Post::whereHas('pinnedPost', function ($query) {
            $query->where('is_pinned', true);
        })->count();

        if ($post->isPinned()) {
            if ($post->isRead() || $post->tags()) {
                $this->isPinned = FALSE;
            }
        }
        //Si le nombre de post épinglé est supérieur à 4, on épingle pas le post et on affiche un message
        else {
            if($countPinnedPosts < 4){
                $this->isPinned = TRUE;
            }else{
                $this->isPinned = FALSE;
                session()->flash('error_alert', 'Le nombre d\'articles épinglés ne doit pas excéder 4');
            }

        }
        if (isset($this->isPinned)) {
            $post->readers()->syncWithoutDetaching([
                auth()->user()->id => [
                    'is_pinned' => $this->isPinned
                ]
            ]);
        }
        else {
            $post->readers()->detach(auth()->user()->id);
        }

    }
}
