<?php

namespace App\Http\Livewire\Modals\Admin\Formats;

use App\Models\Chartnode;
use App\Models\Format;
use App\CustomFacades\AP;
use App\Http\Livewire\WithAlert;
use Livewire\Component;

/**
 * Composant Livewire pour la modale d'édition d'une mise en forme.
 */
class Edit extends Component
{
    use WithAlert;

    /**
     * Modèle de la mise en forme.
     *
     * @var \App\Models\Format
     */
    public $format;

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
     * Mot-clé de recherche pour les nœuds graphiques.
     *
     * @var string
     */
    public $chartnodeSearch = '';

    /**
     * Nœuds graphiques liés sélectionnés dans l'interface.
     *
     * @var array
     */
    public $selectedRelatedChartnodes = [];

    /**
     * Nœuds graphiques disponibles sélectionnés dans l'interface.
     *
     * @var array
     */
    public $selectedAvailableChartnodes = [];

    /**
     * Configuration des onglets du formulaire.
     *
     * @var array
     */
    public $formTabs;

    protected $listeners = ['render'];

    /**
     * Règles de validation pour la mise en forme.
     *
     * @var array
     */
    protected $rules = [
        'format.name' => 'required|string|max:255',
        'format.bg_color' => 'nullable|string|max:255',
        'format.border_style' => 'nullable|string|max:255',
        'format.title_color' => 'nullable|string|max:255',
        'format.subtitle_color' => 'nullable|string|max:255',
    ];

    /**
     * Définit la mise en forme et initialise les onglets du formulaire.
     *
     * @param int|null $id Identifiant de la mise en forme.
     */
    public function setFormat($id = NULL) {
        $this->format = $this->format ?? Format::findOrNew($id);

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.formats',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir la mise en forme",
                    'hidden' => FALSE,
                ],
                'chartnodes' => [
                    'icon' => 'pages',
                    'title' => "Gérer les nœuds correspondant",
                    'hidden' => !$this->format->id,
                ],
            ],
        ];
    }

    /**
     * Initialisation du composant.
     *
     * @param array $data Données contenant l'ID de la mise en forme et éventuellement le mode.
     */
    public function mount($data) {
        extract($data);

        $this->mode = $mode ?? 'view';
        $this->setFormat($id ?? NULL);
    }

    /**
     * Rafraîchit les données (envoie juste une alerte de succès).
     */
    public function refresh() {
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

        if ($mode === 'creation') $this->format = NULL;
        if ($mode !== 'view') $this->setFormat();
    }

    /**
     * Ajoute des nœuds graphiques à la mise en forme actuelle.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     */
    public function add($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'chartnodes' : $this->addSelectedAvailableChartnodes();
            return;
        }
    }

    /**
     * Associe les nœuds graphiques sélectionnés à la mise en forme actuelle.
     */
    public function addSelectedAvailableChartnodes() {
        if ($this->isEmpty('selectedAvailableChartnodes', "Aucun nœud graphique sélectionné")) return;

        Chartnode::query()
            ->whereIn('id', $this->selectedAvailableChartnodes)
            ->update(['format_id' => $this->format->id]);

        $this->selectedAvailableChartnodes = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Association effectuée avec succès."
            ])
            ->self();
    }

    /**
     * Retire des nœuds graphiques de la mise en forme actuelle.
     *
     * @param string $tabsSystem Nom du système d'onglets.
     */
    public function remove($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'chartnodes' : $this->removeSelectedRelatedChartnodes();
            return;
        }
    }

    /**
     * Retire les nœuds graphiques sélectionnés de la mise en forme actuelle.
     */
    private function removeSelectedRelatedChartnodes() {
        if ($this->isEmpty('selectedRelatedChartnodes', "Aucun nœud graphique sélectionné")) return;

        Chartnode::query()
            ->whereIn('id', $this->selectedRelatedChartnodes)
            ->update(['format_id' => NULL]);

        $this->selectedRelatedChartnodes = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Dissociation effectuée avec succès."
            ])
            ->self();
    }

    /**
     * Enregistre la mise en forme ou les modifications.
     */
    public function save() {
        if ($this->mode === 'view') return;

        $this->validate();

        $this->format
            ->save();

        if ($this->mode === 'creation') {
            $this->switchMode('edition');

            $this
                ->sendAlert([
                    'alertClass' => 'success',
                    'message' => "Création de la mise en forme effectuée avec succès."
                ]);
        }
        else {
            $this
                ->sendAlert([
                    'alertClass' => 'success',
                    'message' => "Modification de la mise en forme effectuée avec succès."
                ]);
        }
    }

    /**
     * Récupère la liste des nœuds graphiques disponibles (sans mise en forme).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function availableChartnodes() {
        $search = $this->chartnodeSearch;

        return Chartnode::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->whereNull('format_id')
            ->orderByRaw('name ASC')
            ->get();
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
            view('livewire.modals.admin.formats.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => 'Ajouter une mise en forme',
                'availableChartnodes' => $this->availableChartnodes(),
                'colors' => array_keys(AP::getFormatBgColors()),
                'borders' => AP::getBorderStyles(),
            ]);
    }
}
