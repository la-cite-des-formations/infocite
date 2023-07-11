<?php

namespace App\Http\Livewire\Usage;

use App\Post;
use Livewire\Component;


class SearchPostManager extends Component
{
    public $resultat;
    public function mount($viewBag) {
        $resultat = request()->input('resultat');
        $posts = Post::query()
        ->where('title', 'like', "%$resultat%")
        ->orWhere('content', 'like', "%$resultat%")
        ->get();
        // dd($resultat);
    }

    public function render() {
        return view('livewire.usage.search-post');
    }
}
