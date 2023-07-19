<?php

namespace App\Http\Livewire\Usage;

use App\Post;
use App\Rubric;
use Livewire\Component;
use Livewire\WithPagination;


class SearchResultManager extends Component
{
    use WithPagination;

    public $rubric;
    public $searchedStr;
    protected $paginationTheme = 'bootstrap';
    public $perPageOptions = [8, 10, 25];
    public $perPage = 8;


    public function mount($viewBag) {
        $this->rubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);
        $this->searchedStr = request()->input('resultat');
    }
    public function updatedPerPage() {
        $this->resetPage();
    }

    public function render() {
        return view('livewire.usage.search-result-manager', [
            'foundPosts' => Post::query()
                ->where('title', 'like', "%$this->searchedStr%")
                ->orWhere('content', 'like', "%$this->searchedStr%")
                ->paginate($this->perPage),
            'replaceStr' => '/'. $this->searchedStr . '/i',
        ]);
    }
}
