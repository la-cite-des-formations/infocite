<?php

namespace App\Http\Livewire;

use App\Post;
use App\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

trait WithFilterPosts
{
    public $showFilter = FALSE;

    /**
     * Retourne les posts mis en favorie
     */
    public function favoritePosts()
    {
        return User::find(auth()->user()->id)
            ->myFavoritesPosts()
            ->when($this->mode == 'view', function ($query) {
                $query
                    ->where('published', TRUE)
                    ->where(function ($query) {
                        $query
                            ->where('expired_at', '>', today()->format('Y-m-d'))
                            ->orWhere('expired_at', NULL);
                    });
            })
            ->paginate($this->perPage);
    }

    /**
     * Retourne les posts appartenant aux rubriques misent en favori
     */

    public function postsInFavoritesRubrics(){
        return Post::query()
            ->whereIn('rubric_id', auth()->user()
                ->myFavoritesRubrics()
                ->pluck('id')
            )
            ->orderBy('rubric_id','DESC')
            ->orderBy('created_at', 'DESC')
            ->when($this->mode == 'view', function ($query) {
                $query
                    ->where('published', TRUE)
                    ->where(function ($query) {
                        $query
                            ->where('expired_at', '>', today()->format('Y-m-d'))
                            ->orWhere('expired_at', NULL);
                    });
            })
            ->paginate($this->perPage);
    }

    /**
     * Retourne les posts pas encore consultés
     */
    public function notViewPosts()
    {
        $userId = auth()->user()->id;

        return Post::query()
            ->whereDoesntHave('readers', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orWhereHas('readers', function ($query) use ($userId) {
                $query
                    ->where('user_id', $userId)
                    ->where('is_read', false);
            })
            ->orderBy('updated_at', 'DESC')
            ->when($this->mode == 'view', function ($query) {
                $query
                    ->where('published', TRUE)
                    ->where(function ($query) {
                        $query
                            ->where('expired_at', '>', today()->format('Y-m-d'))
                            ->orWhere('expired_at', NULL);
                    });
            })
            ->paginate($this->perPage);
    }

    /**
     * Retourne les posts les plus consultés
     */
    public function mostConsultedPosts()
    {
        return Post::withCount('readers')
            ->orderByDesc('readers_count')
            ->when($this->mode == 'view', function ($query) {
                $query
                    ->where('published', TRUE)
                    ->where(function ($query) {
                        $query
                            ->where('expired_at', '>', today()->format('Y-m-d'))
                            ->orWhere('expired_at', NULL);
                    });
            })
            ->paginate($this->perPage);
    }

    /**
     * Retourne les posts les plus récemment mient à jours
     */
    public function mostRecentlyPosts(){

        return Post::query()
            ->orderBy('updated_at','DESC')
            ->when($this->mode == 'view', function ($query) {
                $query
                    ->where('published', TRUE)
                    ->where(function ($query) {
                        $query
                            ->where('expired_at', '>', today()->format('Y-m-d'))
                            ->orWhere('expired_at', NULL);
                    });
            })
            ->paginate($this->perPage);
    }

    /**
     * Retourne les posts les plus commentés
     */
    public function mostCommentedPosts()
    {
        return Post::withCount('comments')
            ->orderByDesc('comments_count')
            ->when($this->mode == 'view', function ($query) {
                $query
                    ->where('published', TRUE)
                    ->where(function ($query) {
                        $query
                            ->where('expired_at', '>', today()->format('Y-m-d'))
                            ->orWhere('expired_at', NULL);
                    });
            })
            ->paginate($this->perPage);
    }

    /**
     * Fonction qui s'execute automatiquement dès la mise à jours de la variable $filter
     */
    public function updatedFilter()
    {

        foreach ($this->filter as $key => $value) {
            if ($value == 'on') {
                Session::put('lastFilter', $key);
                $methodName = Str::camel($key);

                if (method_exists($this, $methodName)) {
                    return  $this->posts = $this->{$methodName}();
                }
            }
        }

        return  $this->posts = $this->allPosts();
    }

    /**
     * Fonction qui s'execute automatiquement après la fonction updatedFilter()
     */
    public function updatingFilter(){
        session(['lastSorter'=>null]);
        $this->firstLoad = true;
        $this->resetFilter();
    }



    /**
     * Fonction qui s'execute automatiquement dès la mise à jours de la variable $sorter
     */
    public function updatedSorter()
    {

        foreach ($this->sorter as $key => $value) {
            if ($value == 'on') {
                Session::put('lastSorter', $key);
                $methodName = Str::camel($key);

                if (method_exists($this, $methodName)) {
                    return $this->posts =  $this->{$methodName}();
                }
            }
        }

        return  $this->posts = $this->allPosts();
    }

    /**
     * Fonction qui s'execute automatiquement après la fonction updatingSorter()
     */
    public function updatingSorter(){
        session(['lastFilter'=>null]);
        $this->firstLoad = true;
        $this->resetFilter();
    }

    /**
     * Fonction qui sauvegarde le dernier filtre selectionné dans la session et appelle la fonction de trie/filtre
     * associé en cas de retours sur la page "Une" et si la rubric est la "Une"
     */
    public function lastFilterActive()
    {
        if (Session::get('lastFilter') && $this->rubric->name === 'Une') {

            $filter = Session::get('lastFilter');
            $this->filter[$filter] = 'on';
            $methodName = Str::camel($filter);
            if (method_exists($this, $methodName)) {

                return $this->posts = $this->{$methodName}();
            }
        }
    }

    public function lastSorterActive()
    {
        if (Session::get('lastSorter') && $this->rubric->name === 'Une') {

            $sorter = Session::get('lastSorter');
            $this->sorter[$sorter] = 'on';
            $methodName = Str::camel($sorter);
            if (method_exists($this, $methodName)) {

                return $this->posts = $this->{$methodName}();
            }
        }
    }

    /**
     * Fonction qui réinitialise les filtre en cas d'affichage ou de reduction du menu filtre
     */
    public function toggleFilterMenu(){
        $this->resetFilter();
        $this->filter['allPosts'] = 'on';
        session([
            'lastFilter'=>'allPosts',
            'lastSorter'=>null,
        ]);
        $this->toggleFilter();

    }

    /**
     * Fonction qui réinitialise les filtre
     */
    public function resetFilter(){


        foreach ($this->sorter as $key => $value){
            $this->sorter[$key] = null;
        }
        foreach ($this->filter as $key => $value){
            $this->filter[$key] = null;
        }

    }

    public function updatedWithFilter()
    {
        $this->resetPage();
    }

    public function toggleFilter() {
        $this->showFilter = !$this->showFilter;
    }
}
