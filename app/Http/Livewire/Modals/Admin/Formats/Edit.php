<?php

namespace App\Http\Livewire\Modals\Admin\Formats;

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
    public $formTabs;

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

    public function render()
    {
        return $this->mode === 'view' ?
            view('livewire.modals.admin.formats.sheet') :
            view('livewire.modals.admin.models-form', [
                'addButtonTitle' => 'Ajouter une mise en forme',
                'colors' => array_keys(AP::getFormatBgColors()),
                'borders' => AP::getBorderStyles(),
            ]);
    }
}
