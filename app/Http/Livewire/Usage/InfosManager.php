<?php

namespace App\Http\Livewire\Usage;

use Livewire\Component;
use App\Post;
use App\Rubric;
use App\User;

class InfosManager extends Component
{
    public $rubric;
    // public $elements;
    public $firstLoad = TRUE;
    protected $paginationTheme = 'bootstrap';
    public $filter = [
        'search' => '',
    ];
    public $perPageOptions = [4, 8, 16];
    public $perPage = 8;

    // public $truncateClassesList;
    public function mount($viewBag) {
        $this->rubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);
        // $this->truncateClassesList = $this->userNbClasses > $this->classesMin;
    }
    // public function countFavoritePosts(){

    // }
    public function switchFavoritePost($post_id) {
        if ($this->firstLoad) {
            $this->firstLoad = FALSE;
        }

        $post = Post::find($post_id);

        if ($post->isFavorite()) {
            if ($post->isRead() || $post->tags()) {
                $isFavorite = FALSE;
            }
        }
        else {
            $isFavorite = TRUE;
        }
        if (isset($isFavorite)) {
            $post->readers()->syncWithoutDetaching([
                auth()->user()->id => [
                    'is_favorite' => $isFavorite
                ]
            ]);
        }
        else {
            $post->readers()->detach(auth()->user()->id);
        }
        $this->emitSelf('render');
    }

    public function switchFavoriteRubric() {
        if ($this->firstLoad) $this->firstLoad = FALSE;

        if ($this->rubric->isFavorite()) {
            $this->rubric
                ->users()
                ->detach(auth()->user()->id);
        }
        else {
            $this->rubric
                ->users()
                ->attach(auth()->user()->id);
        }

        $this->emitSelf('render');
    }

    public function updatedPerPage() {
        $this->resetPage();
    }
    public function render() {
        return view('livewire.usage.infos-manager', [
            'user' => User::find(auth()->user()->id),
            'posts' => Post::filter($this->filter)
                ->orderByRaw('title ASC')
                ->paginate($this->perPage),
        ]);
    }
}
