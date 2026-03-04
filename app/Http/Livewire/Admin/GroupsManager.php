<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Models\Group;

/**
 * Composant Livewire pour la gestion des groupes dans l'interface d'administration.
 */
class GroupsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    /**
     * Nom du modèle géré.
     *
     * @var string
     */
    public $models = 'groups';

    /**
     * Nom pluriel des éléments gérés.
     *
     * @var string
     */
    public $elements = 'groups';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    /**
     * Filtres de recherche et de type de groupe.
     *
     * @var array
     */
    public $filter = [
        'searchOnly' => FALSE,
        'search' => '',
        'type' => '',
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
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.models-manager', [
            'groups' => Group::filter($this->filter)
                ->orderByRaw('name ASC')
                ->paginate($this->perPage),
        ]);
    }
}
