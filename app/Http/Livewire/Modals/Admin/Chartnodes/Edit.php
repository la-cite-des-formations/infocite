<?php

namespace App\Http\Livewire\Modals\Admin\Chartnodes;

use App\Models\Format;
use App\Models\Group;
use App\Models\Chartnode;
use App\Http\Livewire\WithAlert;
use Livewire\Component;

/**
 * Composant Livewire pour la modale d'édition d'un nœud graphique.
 */
class Edit extends Component
{
    use WithAlert;

    /**
     * Modèle du nœud graphique.
     *
     * @var \App\Models\Chartnode
     */
    public $chartnode;

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

    /**
     * Règles de validation pour le nœud graphique.
     *
     * @var array
     */
    protected $rules = [
        'chartnode.name' => 'required|string|max:255',
        'chartnode.code_fonction' => 'nullable|integer',
        'chartnode.parent_id' => 'nullable|integer',
        'chartnode.format_id' => 'required|integer',
        'chartnode.rank' => 'required|string|max:20',
    ];

    /**
     * Définit le nœud graphique et initialise les onglets du formulaire.
     *
     * @param int|null $id Identifiant du nœud.
     */
    public function setChartnode($id = NULL) {
        $this->chartnode = $this->chartnode ?? Chartnode::findOrNew($id);

        if (!$this->chartnode->id) {
            $this->chartnode->rank = '-';
        }

        $this->formTabs = [
            'name' => 'formTabs',
            'currentTab' => 'general',
            'panesPath' => 'includes.admin.chartnodes',
            'withMarge' => TRUE,
            'tabs' => [
                'general' => [
                    'icon' => 'list_alt',
                    'title' => "Définir le nœud graphique",
                    'hidden' => FALSE,
                ],
            ],
        ];
    }

    /**
     * Initialisation du composant.
     *
     * @param array $data Données contenant l'ID du nœud et éventuellement le mode.
     */
    public function mount($data) {
        extract($data);

        $this->mode = $mode ?? 'view';
        $this->setChartnode($id ?? NULL);
    }

    /**
     * Rafraîchit les données (envoie juste une alerte de succès).
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
     * Déclenche le dessin de l'organigramme via un événement navigateur.
     */
    public function drawChartnode() {
        $options = [
            'allowCollapse' => TRUE,
            'allowHtml'=> TRUE,
            'size'=> 'small',
            'compactRows' => TRUE,
        ];

        $this->emit('drawOrgChart', 'orgchart', Chartnode::getOrgChart($this->chartnode), $options);
    }

    /**
     * Bascule entre les modes (vue, édition, création).
     *
     * @param string $mode Nouveau mode.
     */
    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode == 'creation') $this->chartnode = NULL;
        if ($mode != 'view') {
            $this->setChartnode();
        }
        else {
            $this->chartnode = Chartnode::find($this->chartnode->id);
            $this->drawChartnode();
        };
    }

    /**
     * Enregistre le nœud graphique ou les modifications.
     */
    public function save() {
        if ($this->mode === 'view') return;

        $this->validate();

        $this->chartnode->save();

        if ($this->mode === 'creation') {
            $this->switchMode('edition');

            $this->sendAlert([
                'alertClass' => 'success',
                'message' => "Création du nœud graphique effectuée avec succès.",
            ]);
        }
        else {
            $this->sendAlert([
                'alertClass' => 'success',
                'message' => "Modification du nœud graphique effectuée avec succès.",
            ]);
        }
    }

    /**
     * Met à jour automatiquement le rang lors du changement de parent.
     */
    public function updatedChartnodeParentId() {
        $this->chartnode->rank = $this->chartnode->parent_id ?
            Chartnode::find($this->chartnode->parent_id)->rank.'-' :
            '-';
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return $this->mode === 'view' ?
            view('livewire.modals.admin.chartnodes.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => 'Ajouter un nœud graphique',
                'groups' => Group::where('type', 'P')->orderBy('name')->get(),
                'parents' => Chartnode::where('id', '!=', $this->chartnode->id)->orderBy('name')->get(),
                'formats' => Format::all(),
            ]);
    }
}
