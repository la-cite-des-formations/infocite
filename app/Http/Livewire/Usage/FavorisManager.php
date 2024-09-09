<?php

namespace App\Http\Livewire\Usage;

use App\Http\Livewire\WithFavoritesHandling;
use App\Http\Livewire\WithModal;
use App\Http\Livewire\WithNotifications;
use App\Http\Livewire\WithUsageMode;
use App\Post;
use App\Rubric;
use App\User;
use Livewire\Component;
use Livewire\WithPagination;

class FavorisManager extends Component
{
    use WithPagination;
    use WithModal;
    use WithNotifications;
    use WithUsageMode;
    use WithFavoritesHandling;

    public $rubric;

    protected $paginationTheme = 'bootstrap';
    public $perPageOptions = [12, 24, 36, 48, 60];
    public $perPage;

    public $rendered = FALSE;
    public $firstLoad = TRUE;
    public $blockRedirection = FALSE;

    public function mount($viewBag) {
        session([
            'backRoute' => request()->getRequestUri(),
            'appsBackRoute' => request()->getRequestUri(),
        ]);
        $this->perPage = session('postsPerPage', 12);
        $this->rubric = $viewBag->rubric;
        $this->setMode();
        $this->setNotifications();

    }

    public function booted()
    {
        $this->firstLoad = !$this->rendered;
    }

    public function redirectToPost($postId) {
        if (!$this->blockRedirection) {
            redirect()->route('post.index', ['rubric' => Post::find($postId)->rubric->route(), 'post_id' => $postId]);
        }
        $this->blockRedirection = FALSE;
    }

    public function removeFavoriteRubric($rubric_id) {
        $this->rubric = Rubric::find($rubric_id);

        if ($this->rubric->isFavorite()) {
            $this->rubric
                ->users()
                ->detach(auth()->user()->id);
        }

        $this->emitSelf('render');
    }

    public function blockRedirection() {
        $this->blockRedirection = TRUE;
    }

    public function render()
    {
        $this->rendered = TRUE;
        $user = User::find(auth()->user()->id);
        $favoritesPosts = $user->myFavoritesPosts()->paginate($this->perPage);
        $favoritesRubrics = $user->myFavoritesRubrics;

        return view('livewire.usage.favoris-manager',[
            'posts' => $favoritesPosts,
            'rubrics' => $favoritesRubrics,
        ]);
    }
}
