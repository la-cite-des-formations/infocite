<?php

namespace App\Http\Livewire\Modals\Admin\Actors;

use App\Models\Actor;
use App\Models\User;
use Livewire\Component;
use App\Http\Livewire\WithAlert;

/**
 * Composant Livewire pour la modale d'édition d'un lien hiérarchique (acteur).
 */
class Edit extends Component
{
    use WithAlert;

    /**
     * Utilisateur concerné par le lien hiérarchique.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * Identifiant du responsable hiérarchique.
     *
     * @var int|null
     */
    public $manager_id;

    /**
     * Mode d'affichage ('view' ou 'edition').
     *
     * @var string
     */
    public $mode;

    /**
     * Indique si l'ajout d'un nouveau lien est autorisé.
     *
     * @var bool
     */
    public $canAdd = FALSE;

    /**
     * Configuration des onglets du formulaire.
     *
     * @var array
     */
    public $formTabs;

    /**
     * Règles de validation pour le responsable hiérarchique.
     *
     * @var array
     */
    protected $rules = [
        'manager_id' => 'nullable',
    ];

    /**
     * Définit l'acteur et initialise les onglets du formulaire.
     *
     * @param int $id Identifiant de l'utilisateur.
     */
    public function setActor($id) {
        $this->user = User::find($id);

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.actors',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir le lien hiérarchique",
                    'hidden' => FALSE,
                ],
            ],
        ];
    }

    /**
     * Initialisation du composant.
     *
     * @param array $data Données contenant l'ID de l'utilisateur et éventuellement le mode.
     */
    public function mount($data) {
        extract($data);

        $this->mode = $mode ?? 'view';
        $this->setActor($id);
        if ($actor = Actor::find($id)) {
            $this->manager_id = $actor->manager_id;
        }
    }

    /**
     * Rafraîchit les données (non implémenté fonctionnellement ici, envoie juste une alerte).
     */
    public function refresh() {
        $this->sendAlert([
            'alertClass' => 'success',
            'message' => "Réinitialisation effectuée avec succès.",
        ]);
    }

    /**
     * Définit l'onglet courant.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     * @param string $tab Identifiant de l'onglet.
     */
    public function setCurrentTab($tabsSystem, $tab) {
        if ($this->$tabsSystem['currentTab'] === $tab) return;

        $this->$tabsSystem['currentTab'] = $tab;
    }

    /**
     * Bascule entre le mode vue et le mode édition.
     *
     * @param string $mode Nouveau mode.
     */
    public function switchMode($mode) {
        $this->mode = $mode;
    }

    /**
     * Enregistre ou supprime le lien hiérarchique.
     */
    public function save() {
        if ($this->mode === 'view') return;

        $this->validate();

        $actor = Actor::findOrNew($this->user->id);
        $actor->id = $actor->id ?? $this->user->id;

        if ($this->manager_id) {
            $actor->manager_id = $this->manager_id;
            $actor->save();
        }
        else {
            $actor->delete();
        }

        $this->sendAlert([
            'alertClass' => 'success',
            'message' => "Lien hiérarchique modifié avec succès.",
        ]);
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return $this->mode === 'view' ?
            view('livewire.modals.admin.actors.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => 'Ajouter un processus fonctionnel',
                'managers' => Actor::getManagers(),
            ]);
    }
}
