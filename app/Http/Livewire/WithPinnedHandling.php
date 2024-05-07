<?php

namespace App\Http\Livewire;

use App\Post;

trait WithPinnedHandling
{
    public $countPinnedPosts;

    public function countPinnedPosts(){

        $this->countPinnedPosts = Post::query()
            ->where('is_pinned',TRUE)
            ->count();
    }

    public function pinnedPosts()
    {
        return Post::query()
            ->where('is_pinned', '=', TRUE)
            ->get();
    }

    public function switchPinnedPost($post_id) {
        $post = $this->post ?? Post::find($post_id);

        //on récupère le nombre d'article épinglé
        $this->countPinnedPosts();

        if ($post->is_pinned) {

            Post::query()
                ->where('id', $post_id)
                ->update(['is_pinned' => FALSE]);

        }
        //Si le nombre de post épinglé est supérieur à 4, on épingle pas le post et on affiche un message
        else {
            if( $this->countPinnedPosts < 4){
                Post::query()
                    ->where('id', $post_id)
                    ->update(['is_pinned' => TRUE]);
            }else{
                session()->flash('error_alert', 'Le nombre d\'articles épinglés ne doit pas excéder 4');
            }

        }
        $this->emitSelf('render');
    }

}
