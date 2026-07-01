<?php

namespace App\Http\Livewire\Modals\Admin\EventTypes;

use App\Models\EventType;
use App\CustomFacades\AP;
use App\Http\Livewire\WithAlert;
use Livewire\Component;

/**
 * Composant Livewire pour la modale d'édition d'un type d'événement.
 */
class Edit extends Component
{
    use WithAlert;

    /**
     * Modèle du type d'événement.
     *
     * @var \App\Models\EventType
     */
    public $eventType;

    /**
     * Mode d'affichage ou d'édition.
     *
     * @var string
     */
    public $mode;

    /**
     * Indique si l'ajout est autorisé.
     *
     * @var bool
     */
    public $canAdd = TRUE;

    /**
     * Configuration des onglets du formulaire.
     *
     * @var array
     */
    public $formTabs;

    protected $listeners = ['render'];

    /**
     * Règles de validation pour le type d'événement.
     *
     * @var array
     */
    protected $rules = [
        'eventType.name' => 'required|string|max:100',
        'eventType.color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
    ];

    /**
     * Définit le type d'événement et initialise les onglets du formulaire.
     *
     * @param int|null $id Identifiant du type d'événement.
     */
    public function setEventType($id = NULL) {
        $this->eventType = $this->eventType ?? EventType::findOrNew($id);
        
        if (!$this->eventType->exists) {
            $this->eventType->color = '#3498db'; // default color
        }

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.event-types',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir le type d'événement",
                    'hidden' => FALSE,
                ],
            ],
        ];
    }

    /**
     * Initialisation du composant.
     *
     * @param array $data Données contenant l'ID du type d'événement et éventuellement le mode.
     */
    public function mount($data) {
        extract($data);

        $this->mode = $mode ?? 'view';
        $this->setEventType($id ?? NULL);
    }

    /**
     * Rafraîchit les données (envoie juste une alerte de succès).
     */
    public function refresh() {
        $this->eventType = EventType::findOrNew($this->eventType->id);
        $this
            ->sendAlert([
                'alertClass' => 'success',
                'message' => "Réinitialisation effectuée avec succès."
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
     * Bascule entre les modes (vue, édition, création).
     *
     * @param string $mode Nouveau mode.
     */
    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode === 'creation') $this->eventType = NULL;
        if ($mode !== 'view') $this->setEventType();
    }

    /**
     * Enregistre le type d'événement.
     */
    public function save() {
        if ($this->mode === 'view') return;

        $this->validate();

        $this->eventType->save();

        if ($this->mode === 'creation') {
            $this->switchMode('edition');

            $this
                ->sendAlert([
                    'alertClass' => 'success',
                    'message' => "Création du type d'événement effectuée avec succès."
                ]);
        }
        else {
            $this
                ->sendAlert([
                    'alertClass' => 'success',
                    'message' => "Modification du type d'événement effectuée avec succès."
                ]);
        }
    }

    /**
     * Rendu du composant.
     *
     * @param array|null $messageBag Sac de messages d'alerte (optionnel).
     * @return \Illuminate\View\View
     */
    public function render($messageBag = NULL)
    {
        if ($messageBag) {
            $this->sendAlert($messageBag);
        }

        return $this->mode === 'view' ?
            view('livewire.modals.admin.event-types.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => "Ajouter un type d'événement",
            ]);
    }
}
