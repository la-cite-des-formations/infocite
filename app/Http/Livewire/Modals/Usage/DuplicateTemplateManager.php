<?php

namespace App\Http\Livewire\Modals\Usage;

use App\Models\Post;
use Livewire\Component;

class DuplicateTemplateManager extends Component
{
    public $templateId;
    public $newName;
    public $saveOriginal = true;
    public $fromEdit = false;
    public $currentContent;

    public function mount($data)
    {
        $this->templateId = $data['templateId'] ?? null;
        $this->fromEdit = $data['fromEdit'] ?? false;
        $this->currentContent = $data['currentContent'] ?? null;
        
        $template = Post::find($this->templateId);
        if ($template) {
            $this->newName = "Copie de " . $template->title;
        }
    }

    public function duplicate($andEdit = false)
    {
        $original = Post::findOrFail($this->templateId);
        
        // Si on vient de l'édition et que l'utilisateur a choisi de sauvegarder l'original
        if ($this->fromEdit && $this->saveOriginal && $this->currentContent !== null) {
            $original->content = $this->currentContent;
            $original->save();
        }

        $newPost = $original->replicate();
        $newPost->title = $this->newName ?: "Copie de " . $original->title;
        
        // Si on a du contenu frais (depuis l'éditeur), on l'utilise pour la copie
        if ($this->currentContent !== null) {
            $newPost->content = $this->currentContent;
        }
        
        $newPost->save();

        $this->emit('modalClosed');
        
        if ($andEdit) {
            return redirect()->route('post.edit', [
                'rubric' => $newPost->rubric ? $newPost->rubric->segmentPath() : 'une', 
                'post_id' => $newPost->id
            ]);
        }

        $this->emit('refreshPage');
    }

    public function render()
    {
        return view('livewire.modals.usage.duplicate-template-manager');
    }
}
