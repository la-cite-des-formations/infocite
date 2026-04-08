<?php

namespace App\Http\Livewire\Usage;

use App\Models\Post;
use Livewire\Component;

/**
 * Composant Livewire pour la notation d'un article par étoiles.
 */
class PostRating extends Component
{
    /** @var Post L'article à noter */
    public $post;

    /** @var int La note actuelle de l'utilisateur (0-5) */
    public $rating;

    /**
     * Initialise le composant avec l'article et récupère la note actuelle du lecteur.
     *
     * @param Post $post
     */
    public function mount(Post $post)
    {
        $this->post = $post;
        $this->rating = $this->post->userRating();
    }

    /**
     * Enregistre ou met à jour la note de l'utilisateur.
     * Si l'utilisateur clique sur sa note actuelle, celle-ci est remise à 0.
     *
     * @param int $value
     */
    public function setRating($value)
    {
        if ($value < 1 || $value > 5) return;

        if ($this->rating == $value) {
            $this->rating = 0;
        } else {
            $this->rating = $value;
        }

        $this->post->readers()->syncWithoutDetaching([
            auth()->id() => [
                'rating' => $this->rating
            ]
        ]);

        $this->emit('ratingUpdated');
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.usage.post-rating');
    }
}
