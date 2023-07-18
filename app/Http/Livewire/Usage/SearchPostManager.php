<?php

namespace App\Http\Livewire\Usage;

use App\Post;
use Livewire\Component;
use Livewire\WithPagination;


class SearchPostManager extends Component
{
    use WithPagination;

    public $rubric;
    public $searchedStr;
    protected $paginationTheme = 'bootstrap';
    public $perPageOptions = [8, 10, 25];
    public $perPage = 8;

    public function mount() {
        $this->searchedStr = request()->input('resultat');
    }
    public function updatedPerPage() {
        $this->resetPage();
    }

    // public function highlightResearch($string)
    // {
    //     if (strtolower($string) || strtoupper($string[0]))
    //     {
    //         str_replace($string, " &thinsp; <strong>$$string</strong> &thinsp;", Post::preview());
    //     }
    // }

    public function render() {
        return view('livewire.usage.search-post', [
            'foundPosts' => Post::query()
                ->where('title', 'like', "%$this->searchedStr%")
                ->orWhere('content', 'like', "%$this->searchedStr%")
                ->paginate($this->perPage),
            'replaceStr' => '/'. $this->searchedStr . '/i',
        ]);
    }
}
