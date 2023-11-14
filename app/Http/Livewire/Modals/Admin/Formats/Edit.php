<?php

namespace App\Http\Livewire\Modals\Admin\Formats;

use App\CustomFacades\AP;
use App\Format;
use App\Http\Livewire\WithAlert;
use App\Process;
use Livewire\Component;

class Edit extends Component
{
    use WithAlert;

    public $format;
    public $mode;
    public $canAdd = TRUE;
    public $processSearch = '';
    public $selectedRelatedProcesses = [];
    public $selectedAvailableProcesses = [];
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
                'processes' => [
                    'icon' => 'developer_board',
                    'title' => "Gérer les processus correspondant",
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
            case 'processes' : $this->addSelectedAvailableProcesses();
            return;
        }
    }

    public function addSelectedAvailableProcesses() {
        if ($this->isEmpty('selectedAvailableProcesses', "Aucun processus sélectionné")) return;

        Process::query()
            ->whereIn('id', $this->selectedAvailableProcesses)
            ->update(['format_id' => $this->format->id]);

        $this->selectedAvailableProcesses = [];

        $this
            ->emit('render', [
                'alertClass' => 'success',
                'message' => "Association effectuée avec succès."
            ])
            ->self();
    }

    public function remove($tabsSystem) {
        switch($this->$tabsSystem['currentTab']) {
            case 'processes' : $this->removeSelectedRelatedProcesses();
            return;
        }
    }

    private function removeSelectedRelatedProcesses() {
        if ($this->isEmpty('selectedRelatedProcesses', "Aucun processus sélectionné")) return;

        Process::query()
            ->whereIn('id', $this->selectedRelatedProcesses)
            ->update(['format_id' => NULL]);

        $this->selectedRelatedProcesses = [];

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

    private function availableProcesses() {
        $search = $this->processSearch;
        return Process::query()
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
                'availableProcesses' => $this->availableProcesses(),
                'colors' => array_keys(AP::getFormatBgColors()),
                'borders' => AP::getBorderStyles(),
            ]);
    }
}
