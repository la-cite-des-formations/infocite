<?php

namespace App\Http\Livewire\Usage;

use Livewire\Component;

/**
 * Composant Livewire pour la recherche globale d'articles.
 */
class SearchManager extends Component
{
    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.usage.search-manager');
    }
}
