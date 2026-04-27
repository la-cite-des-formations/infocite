<?php

namespace App\Http\Livewire\Modals\Usage;

use App\Http\Livewire\WithModal;
use App\Models\Post;
use Livewire\Component;

class ApplyTemplateManager extends Component
{
    use WithModal;

    public $currentContent;
    public $rubricId;
    public $selectedTemplateId = '';

    public function mount($data) {
        $this->currentContent = $data['currentContent'] ?? '';
        $this->rubricId = $data['rubricId'] ?? null;
    }

    public function apply() {
        if ($this->selectedTemplateId) {
            $this->emit('applyTemplateConfirmed', $this->selectedTemplateId);
        }
    }

    public function render()
    {
        $templates = Post::templates()
            ->when($this->rubricId, function($query) {
                $query->where(function($q) {
                    $q->whereNull('rubric_id')
                      ->orWhere('rubric_id', $this->rubricId);
                });
            })
            ->orderBy('title')
            ->get();

        $selectedTemplate = $this->selectedTemplateId ? $templates->firstWhere('id', $this->selectedTemplateId) : null;

        return view('livewire.modals.usage.apply-template-manager', [
            'templates' => $templates,
            'selectedTemplate' => $selectedTemplate,
        ]);
    }
}
