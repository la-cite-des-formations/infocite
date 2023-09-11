<?php

namespace App\Http\Livewire\Usage;

use Livewire\Component;
use App\Post;
use App\Rubric;
use App\User;
use Livewire\WithPagination;

class InfosManager extends Component
{
    use WithPagination;

    // public $elements;
    public $rubric;
    public $firstLoad = TRUE;
    protected $paginationTheme = 'bootstrap';
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

    public function switchFavoriteRubric($rubric_id) {
        if ($this->firstLoad) $this->firstLoad = FALSE;

        $this->rubric = Rubric::find($rubric_id);

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
        $user = User::find(auth()->user()->id);
        return view('livewire.usage.infos-manager', [
            'user' => $user,
            'favoritesPosts' => $user->myFavoritesPosts()
                ->paginate($this->perPage),
            'favoritesRubrics' => $user->rubrics()
                ->paginate($this->perPage),
        ]);
    }
}
