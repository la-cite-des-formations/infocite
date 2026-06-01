<?php

namespace App\Http\Livewire\Modals\Usage;

use Livewire\Component;
use App\Models\Post;

/**
 * Composant Livewire affichant la liste des utilisateurs ayant acquitté un article.
 * Utilisé via la modale déclenchée par le bouton « Acquitté » sur l'interface publique.
 */
class PostAcknowledgers extends Component
{
    public Post $post;

    /**
     * Initialisation : récupère l'article depuis les données transmises par la modale.
     *
     * @param array $data Données contenant l'id de l'article.
     */
    public function mount($data)
    {
        $this->post = Post::find($data['id']);
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.modals.usage.post-acknowledgers', [
            'acknowledgers' => $this->post
                ? $this->post->acknowledgers()->orderByPivot('occurred_at', 'desc')->get()
                : collect()
        ]);
    }
}
