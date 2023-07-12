<?php

namespace App\Http\Livewire\Usage;

use App\Post;
use Livewire\Component;
// use Livewire\WithPagination;


class SearchPostManager extends Component
{
    // use WithPagination;

    public $rubric;
    public $foundPosts;
    public $searchedStr;
    // public $perPageOptions = [8, 12, 16];
    // public $perPage = 8;

    public function mount($viewBag) {
        $this->searchedStr = request()->input('resultat');
        $this->foundPosts = Post::query()
        ->where('title', 'like', "%$this->searchedStr%")
        ->orWhere('content', 'like', "%$this->searchedStr%")
        ->get();
        // ->paginate($this->perPage);
    }

    public function render() {
        return view('livewire.usage.search-post');
    }
}
