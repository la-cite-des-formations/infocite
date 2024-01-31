<?php

namespace App\Http\Livewire\Usage;

use App\Post;
use App\Rubric;
use App\User;
use Livewire\Component;
use Livewire\WithPagination;


class SearchResultManager extends Component
{
    use WithPagination;

    public $rubric;
    public $firstLoad = TRUE;
    public $searchedStr;
    protected $paginationTheme = 'bootstrap';
    public $perPageOptions = [8, 10, 25];
    public $perPage;


    public function mount($viewBag) {
        session(['appsBackRoute' => request()->getRequestUri()]);
        $this->perPage = session('searchResultPerPage', 8);
        $this->rubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);
        $this->searchedStr = request()->input('resultat');
    }

    public function updatedPerPage() {
        session(['searchResultPerPage' => $this->perPage]);

        $this->resetPage();
    }

    public function render() {
        return view('livewire.usage.search-result-manager', [
            'foundPosts' => Post::query()
                ->whereIn('id', Post::query()
                    ->where('title', 'like', "%$this->searchedStr%")
                    ->orWhere('content', 'like', "%$this->searchedStr%")
                    ->get()
                    ->filter(function ($post) {
                        return User::find(auth()->user()->id)->can('read', $post);
                    })
                    ->pluck('id')
                )
                ->paginate($this->perPage),
            'replaceStr' => '/'. $this->searchedStr . '/i',
        ]);
    }
}
