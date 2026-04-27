<?php

namespace App\Http\Livewire\Modals\Usage;

use App\Http\Livewire\WithModal;
use Livewire\Component;

class ExtractTemplateManager extends Component
{
    use WithModal;

    public $currentContent;
    public $isDirty;
    public $saveArticleBeforeRedirect = true;

    public function mount($data) {
        $this->currentContent = $data['currentContent'] ?? '';
        $this->isDirty = $data['isDirty'] ?? false;
    }

    public function extractAndStay() {
        $this->emit('extractTemplateConfirmed', false, false);
    }

    public function extractAndRedirect() {
        $this->emit('extractTemplateConfirmed', true, $this->saveArticleBeforeRedirect);
    }

    public function render()
    {
        return view('livewire.modals.usage.extract-template-manager');
    }
}
