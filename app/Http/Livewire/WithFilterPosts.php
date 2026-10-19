<?php

namespace App\Http\Livewire;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

trait WithFilterPosts
{
    /**
     * État d'affichage du menu de filtrage.
     *
     * @var bool
     */
    public $showFilter = FALSE;

    /**
     * Retourne les articles mis en favoris.
     */
    /**
     * Récupère les articles mis en favoris par l'utilisateur.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function favoritePosts()
    {
        return User::find(auth()->user()->id)
            ->favoritePosts()
            ->notTemplates()
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
     * Retourne les articles appartenant aux rubriques mises en favoris.
     */

    /**
     * Récupère les articles appartenant aux rubriques mises en favoris par l'utilisateur.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function postsInFavoritesRubrics(){
        return Post::query()
            ->notTemplates()
            ->whereIn('rubric_id', auth()->user()
                ->favoriteRubrics()
                ->pluck('favoriteable_id')
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
     * Retourne les articles pas encore consultés.
     */
    /**
     * Récupère les articles qui n'ont pas encore été consultés par l'utilisateur.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function notViewPosts()
    {
        $userId = auth()->user()->id;

        return Post::query()
            ->notTemplates()
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
     * Retourne les articles pas encore acquittés.
     */
    public function notAcknowledgedPosts()
    {
        $userId = auth()->id();

        return Post::query()
            ->notTemplates()
            ->where('is_acknowledgment_required', TRUE)
            ->whereDoesntHave('acknowledgers', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('published_at', 'DESC')
            ->when($this->mode == 'view', function ($query) {
                $query
                    ->where('published', TRUE)
                    ->where(function ($query) {
                        $query
                            ->where('expired_at', '>', today()->format('Y-m-d'))
                            ->orWhereNull('expired_at');
                    });
            })
            ->paginate($this->perPage);
    }

    /**
     * Retourne les articles les plus consultés.
     */
    /**
     * Récupère les articles les plus consultés globalement.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function mostConsultedPosts()
    {
        return Post::query()
            ->notTemplates()
            ->withCount('readers')
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
     * Retourne les articles les plus récemment mis à jour.
     */
    /**
     * Récupère les articles les plus récemment mis à jour.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function mostRecentlyPosts(){

        return Post::query()
            ->notTemplates()
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
     * Retourne les articles les plus commentés.
     */
    /**
     * Récupère les articles les plus commentés globalement.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function mostCommentedPosts()
    {
        return Post::query()
            ->notTemplates()
            ->withCount('comments')
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
     * Fonction qui s'exécute automatiquement dès la mise à jour de la variable $filter.
     */
    /**
     * Se déclenche lors de la mise à jour des filtres et applique dynamiquement la méthode correspondante.
     *
     * @return mixed Les articles filtrés ou tous les articles.
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
     * Fonction qui s'exécute automatiquement après la fonction updatedFilter().
     */
    /**
     * Se déclenche avant la mise à jour des filtres pour réinitialisation.
     */
    public function updatingFilter(){
        session()->forget('lastSorter');
        $this->firstLoad = true;
        $this->resetFilter();
    }

    /**
     * Fonction qui s'exécute automatiquement dès la mise à jour de la variable $sorter.
     */
    /**
     * Se déclenche lors de la mise à jour du tri et applique dynamiquement la méthode correspondante.
     *
     * @return mixed Les articles triés ou tous les articles.
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
     * Fonction qui s'exécute automatiquement après la fonction updatingSorter().
     */
    /**
     * Se déclenche avant la mise à jour du tri pour réinitialisation.
     */
    public function updatingSorter(){
        session()->forget('lastFilter');
        $this->firstLoad = true;
        $this->resetFilter();
    }

    /**
     * Fonction qui sauvegarde le dernier filtre sélectionné dans la session et appelle la fonction de tri/filtre
     * associé en cas de retour sur la page "Une" et si la rubrique est la "Une".
     */
    /**
     * Restaure et applique le dernier filtre actif depuis la session.
     *
     * @return mixed Les articles filtrés si applicables.
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

    /**
     * Restaure le dernier tri actif depuis la session.
     *
     * @return mixed
     */
    /**
     * Restaure et applique le dernier tri actif depuis la session.
     *
     * @return mixed Les articles triés si applicables.
     */
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
     * Fonction qui réinitialise les filtres en cas d'affichage ou de réduction du menu filtre.
     */
    /**
     * Alterne l'affichage du menu de filtre et réinitialise les filtres actifs.
     */
    public function toggleFilterMenu(){
        $this->resetFilter();
        $this->filter['allPosts'] = 'on';
        session([
            'lastFilter'=>'allPosts',
        ]);
        session()->forget('lastSorter');
        $this->toggleFilter();

    }

    /**
     * Fonction qui réinitialise les filtres.
     */
    /**
     * Réinitialise tous les états de filtres et de tri.
     */
    public function resetFilter(){
        foreach ($this->sorter as $key => $value){
            $this->sorter[$key] = null;
        }
        foreach ($this->filter as $key => $value){
            $this->filter[$key] = null;
        }
    }

    /**
     * Se déclenche lors de la mise à jour des filtres et réinitialise la pagination.
     */
    public function updatedWithFilter()
    {
        $this->resetPage();
    }

    /**
     * Alterne l'état d'affichage du filtre.
     */
    public function toggleFilter() {
        $this->showFilter = !$this->showFilter;
    }
}
