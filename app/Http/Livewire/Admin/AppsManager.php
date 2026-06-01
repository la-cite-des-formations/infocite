<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Livewire\WithFilter;
use App\Http\Livewire\WithModal;
use App\Models\App;

/**
 * Composant Livewire pour la gestion des applications dans l'interface d'administration.
 */
class AppsManager extends Component
{
    use WithPagination;
    use WithFilter;
    use WithModal;

    /**
     * Nom du modèle géré.
     *
     * @var string
     */
    public $models = 'apps';

    /**
     * Nom pluriel des éléments gérés.
     *
     * @var string
     */
    public $elements = 'apps';

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['modalClosed', 'render'];

    /**
     * Filtres de recherche et de type d'application.
     *
     * @var array
     */
    public $filter = [
        'searchOnly' => FALSE,
        'authType' => '',
        'type' => 'I', // applications institutionnelles par défaut
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
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.models-manager', [
            'apps' => App::filter($this->filter)
                ->paginate($this->perPage),
        ]);
    }
}
