<?php

namespace App\Http\Livewire\Usage;

use App\Models\App;
use App\Http\Livewire\WithModal;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Composant Livewire pour la gestion et l'affichage des applications côté usage.
 */
class AppsManager extends Component
{
    use WithPagination;
    use WithModal;

    /**
     * Segment de rubrique actuel.
     *
     * @var string
     */
    public $rubricSegment;

    /**
     * Indique si le composant a été rendu.
     *
     * @var bool
     */
    public $rendered = FALSE;

    /**
     * Indique si c'est le premier chargement.
     *
     * @var bool
     */
    public $firstLoad = TRUE;

    /**
     * Indique si la redirection est bloquée.
     *
     * @var bool
     */
    public $blockRedirection = FALSE;

    /**
     * Écouteurs d'événements.
     *
     * @var array
     */
    protected $listeners = ['deleteApp', 'render', 'displayUpdated'=>'render'];

    /**
     * Initialisation du composant.
     *
     * @param object $viewBag Sac de données contenant le segment de rubrique.
     */
    public function mount($viewBag) {
        $this->rubricSegment = $viewBag->rubricSegment;
    }

    /**
     * Détermine si c'est le premier chargement du composant.
     */
    public function booted() {
        $this->firstLoad = !$this->rendered;
    }

    /**
     * Ajoute ou retire une application des favoris de l'utilisateur.
     * Gère également le rang des applications favorites.
     *
     * @param int $appId Identifiant de l'application.
     */
    public function switchFavoriteApp($appId) {
        $app = App::find($appId);
        $user = auth()->user();
        $updatedFavoritesApps = $user->myFavoritesApps->pluck('pivot.rank', 'id');

        if ($app->isFavorite) {
            $currentRank = $updatedFavoritesApps->pull($appId);
            $updatedFavoritesApps = $updatedFavoritesApps->map(function ($rank, $id) use ($currentRank) {
                return ['rank' => $rank < $currentRank ? $rank : $rank - 1];
            });
        }
        else {
            $updatedFavoritesApps->put($appId, 0);
            $updatedFavoritesApps = $updatedFavoritesApps->map(function ($rank, $id) {
                return ['rank' => $rank + 1];
            });
        }

        $user->myFavoritesApps()->sync($updatedFavoritesApps);

        $this->emitSelf('render');
    }

    /**
     * Supprime une application personnelle.
     *
     * @param int $appId Identifiant de l'application.
     */
    public function deleteApp($appId) {
        App::find($appId)->delete();
    }

    /**
     * Redirige vers l'URL de l'application dans un nouvel onglet.
     *
     * @param string $appUrl URL cible.
     */
    public function redirectToApp($appUrl) {
        if (!$this->blockRedirection) {
            $this->emit('newTabRedirection', $appUrl);
        }
        else {
            $this->blockRedirection = FALSE;
        }
    }

    /**
     * Bloque la redirection automatique (utile lors du clic sur un bouton d'action).
     */
    public function blockRedirection() {
        $this->blockRedirection = TRUE;
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render() {
        $this->rendered = TRUE;

        return view('livewire.usage.apps-manager');
    }
}
