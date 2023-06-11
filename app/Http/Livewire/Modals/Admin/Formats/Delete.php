<?php

namespace App\Http\Livewire\Modals\Admin\Formats;

use App\Format;
use Livewire\Component;

class Delete extends Component
{
    public $formatsIDs;
    public $deletionPerformed = FALSE;

    public function mount($formatsIDs) {
        $this->formatsIDs = $formatsIDs;
    }

    public function delete() {
        Format::whereIn('id', $this->formatsIDs)->delete();

        $this->deletionPerformed = TRUE;
    }

    public function render()
    {
        return view('livewire.modals.admin.delete-models', [
            'headerModelsList' => count($this->formatsIDs) > 1 ? 'Mises en forme concernées' : 'Mise en forme concernée',
            'models' => Format::query()
                ->whereIn('id', $this->formatsIDs)
                ->orderByRaw('name ASC')
                ->get(),
            'modelInfo' => ['field' => 'name'],
        ]);
    }
}
