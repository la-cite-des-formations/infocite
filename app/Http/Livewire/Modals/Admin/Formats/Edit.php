<?php

namespace App\Http\Livewire\Modals\Admin\Formats;

use App\Chartnode;
use App\CustomFacades\AP;
use App\Format;
use App\Http\Livewire\WithAlert;
use Livewire\Component;

class Edit extends Component
{
    use WithAlert;

    public $format;
    public $mode;
    public $canAdd = TRUE;
    public $chartnodeSearch = '';
    public $selectedRelatedChartnodes = [];
    public $selectedAvailableChartnodes = [];
    public $formTabs;

    protected $listeners = ['render'];

    protected $rules = [
        'format.name' => 'required|string|max:255',
        'format.bg_color' => 'nullable|string|max:255',
        'format.border_style' => 'nullable|string|max:255',
        'format.title_color' => 'nullable|string|max:255',
        'format.subtitle_color' => 'nullable|string|max:255',
    ];

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

    public function mount($data) {
        extract($data);

        $this->mode = $mode ?? 'view';
        $this->setFormat($id ?? NULL);
    }

    public function refresh() {
        $this
            ->sendAlert([
                'alertClass' => 'success',
                'message' => "Réinitialisation effectuée avec succès."
            ]);
    }

    public function setCurrentTab($tabsSystem, $tab) {
        if ($this->$tabsSystem['currentTab'] === $tab) return;

        $this->$tabsSystem['currentTab'] = $tab;
    }

    public function switchMode($mode) {
        $this->mode = $mode;

        if ($mode === 'creation') $this->format = NULL;
        if ($mode !== 'view') $this->setFormat();
    }

    public function add($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'chartnodes' : $this->addSelectedAvailableChartnodes();
            return;
        }
    }

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

    public function remove($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'chartnodes' : $this->removeSelectedRelatedChartnodes();
            return;
        }
    }

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
