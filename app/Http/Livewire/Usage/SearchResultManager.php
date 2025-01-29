<?php

namespace App\Http\Livewire\Usage;

use App\App;
use App\Post;
use App\Rubric;
use App\User;
use Livewire\Component;
use Livewire\WithPagination;


class SearchResultManager extends Component
{
    use WithPagination;

    public $rubric;
    public $rendered = FALSE;
    public $firstLoad = TRUE;
    public $blockRedirection = FALSE;
    public $searchedStr;
    protected $paginationTheme = 'bootstrap';
    public $perPageOptions = [8, 10, 25];
    public $postsPerPage;
    public $appsPerPage;


    public function mount($viewBag) {
        session(['appsBackRoute' => request()->getRequestUri()]);
        $this->postsPerPage = session('searchResultPostsPerPage', 8);
        $this->appsPerPage = session('searchResultAppsPerPage', 8);
        $this->rubric = Rubric::firstWhere('segment', $viewBag->rubricSegment);
        $this->searchedStr = request()->input('searchedStr');
    }

    public function booted()
    {
        $this->firstLoad = !$this->rendered;
    }

    public function redirectToPost($postId)
    {
        redirect()->route('post.index', ['rubric' => Post::find($postId)->rubric->route(), 'post_id' => $postId]);
    }

    public function redirectToApp($appUrl) {
        if (!$this->blockRedirection) {
            $this->emit('newTabRedirection', $appUrl);
        }
        else {
            $this->blockRedirection = FALSE;
        }
    }

    public function blockRedirection() {
        $this->blockRedirection = TRUE;
    }

    public function updatedPostsPerPage() {
        session(['searchResultPostsPerPage' => $this->postsPerPage]);

        $this->resetPage('foundPostsPage');
    }

    public function updatedAppsPerPage() {
        session(['searchResultAppsPerPage' => $this->appsPerPage]);

        $this->resetPage('foundAppsPage');
    }

    public function render() {
        $this->rendered = TRUE;

        return view('livewire.usage.search-result-manager', [
            'foundPosts' => Post::query()
                ->whereIn('id', Post::query()
                    ->where('title', 'like', "%$this->searchedStr%")
                    ->orWhere('content', 'like', "%$this->searchedStr%")
                    ->get()
                    ->filter(function ($post) {
                        return auth()->user()->can('read', $post);
                    })
                    ->pluck('id')
                )
                ->paginate($this->postsPerPage, '*', 'foundPostsPage'),
            'foundApps' => App::query()
                ->whereIn('id', App::query()
                    ->where(function ($query) {
                        $query
                            ->whereNull('owner_id')
                            ->orWhere('owner_id', auth()->user()->id);
                    })
                    ->where(function ($query) {
                        $query
                            ->where('name', 'like', "%$this->searchedStr%")
                            ->orWhere('description', 'like', "%$this->searchedStr%")
                            ->orWhere('url', 'like', "%$this->searchedStr%");
                    })
                    ->get()
                    ->filter(function ($app) {
                        return auth()->user()->can('view', $app);
                    })
                    ->pluck('id')
                )
                ->paginate($this->appsPerPage, '*', 'foundAppsPage'),
            'replaceStr' => '/'. $this->searchedStr . '/i',
        ]);
    }
}
