<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Http\Livewire\WithSearching;
use App\Models\User;

/**
 * Composant Livewire pour la gestion des référents (labels) dans l'interface d'administration.
 */
class LabelsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;
    use WithSearching;

    /**
     * Nom du modèle géré.
     *
     * @var string
     */
    public $models = 'labels';

    /**
     * Nom pluriel des éléments gérés.
     *
     * @var string
     */
    public $elements = 'referents';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    /**
     * Filtres de recherche pour les référents.
     *
     * @var array
     */
    public $filter = [
        'searchOnly' => TRUE,
        'search' => '',
    ];

    /**
     * Options pour le nombre d'éléments par page.
     *
     * @var array
     */
    public $perPageOptions = [10, 15, 25];

    /**
     * Nombre d'éléments par page sélectionné.
     *
     * @var int
     */
    public $perPage = 10;

    /**
     * Récupère la liste des référents filtrés.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation
     */
    private function getReferents() {
        $search = $this->filter['search'];
        $referents = User::allWho('have-label')
            ->get()
            ->when($search, function ($referents) use ($search) {
                return $referents->filter(function ($referent) use ($search) {
                    return static::tableContains([
                        $referent->label,
                        $referent->identity,
                        $referent->process,
                    ], $search);
                });
            });

        if ($referents->isEmpty()) {
            return User::query()->whereNull('id');
        }

        return $referents->toQuery();
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.models-manager', [
            'referents' => $this
                ->getReferents()
                ->paginate($this->perPage),
            'dashboard' => 'org-chart',
        ]);
    }
}
