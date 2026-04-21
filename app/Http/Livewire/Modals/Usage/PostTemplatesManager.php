<?php

namespace App\Http\Livewire\Modals\Usage;

use App\Http\Livewire\WithModal;
use App\Models\Post;
use Livewire\Component;

/**
 * Modale de gestion des modèles d'articles.
 * Permet de lister les modèles existants et de les supprimer.
 */
class PostTemplatesManager extends Component
{
    use WithModal;

    /**
     * ID de la rubrique courante pour filtrer les modèles.
     *
     * @var int|null
     */
    public $rubricId;

    /**
     * Filtre de rubrique sélectionné dans la vue (si affiché).
     *
     * @var string
     */
    public $filterRubricId = 'all';

    /**
     * Initialisation du composant.
     *
     * @param array $data Données contenant l'ID de la rubrique.
     */
    public function mount($data) {
        $this->rubricId = $data['rubricId'] ?? null;
    }

    /**
     * Supprime un modèle d'article.
     *
     * @param int $id ID du modèle à supprimer.
     */
    public function deleteTemplate($id) {
        $template = Post::templates()->findOrFail($id);
        $template->delete();
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $currentRubric = $this->rubricId ? \App\Models\Rubric::find($this->rubricId) : null;
        $isUne = $currentRubric && $currentRubric->name === 'Une';

        $targetCreateRoute = 'une';
        if ($isUne && is_numeric($this->filterRubricId)) {
            $filteredRubric = \App\Models\Rubric::find($this->filterRubricId);
            if ($filteredRubric) {
                $targetCreateRoute = $filteredRubric->route();
            }
        } elseif (!$isUne && $currentRubric) {
            $targetCreateRoute = $currentRubric->route();
        }

        return view('livewire.modals.usage.post-templates-manager', [
            'rubric' => $currentRubric,
            'isUne' => $isUne,
            'targetCreateRoute' => $targetCreateRoute,
            'allRubrics' => $isUne ? \App\Models\Rubric::query()
                ->where('contains_posts', TRUE)
                ->where('rank', '!=', '0')
                ->orderByRaw('position ASC, rank ASC')
                ->get() : collect(),
            'templates' => Post::templates()
                ->when(!$isUne, function($query) {
                    $query->where(function($q) {
                        $q->whereNull('rubric_id')
                          ->orWhere('rubric_id', $this->rubricId);
                    });
                })
                ->when($isUne && $this->filterRubricId !== 'all', function($query) {
                    if ($this->filterRubricId === 'global') {
                        $query->whereNull('rubric_id');
                    } else {
                        $query->where('rubric_id', $this->filterRubricId);
                    }
                })
                ->with('rubric')
                ->get()
                ->sortBy(function ($template) {
                    $rubricName = $template->rubric_id ? $template->rubric->name : '000_Global';
                    return $rubricName . '_' . $template->title;
                })
        ]);
    }
}
