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

    protected $listeners = ['deleteTemplate'];

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
                $targetCreateRoute = $filteredRubric->segmentPath();
            }
        } elseif (!$isUne && $currentRubric) {
            $targetCreateRoute = $currentRubric->segmentPath();
        }

        $user = auth()->user();
        $myRubricsIds = $user->myRubrics()->pluck('id')->toArray();

        return view('livewire.modals.usage.post-templates-manager', [
            'rubric' => $currentRubric,
            'isUne' => $isUne,
            'targetCreateRoute' => $targetCreateRoute,
            'allRubrics' => $isUne ? \App\Models\Rubric::query()
                ->select('id', 'name')
                ->where('contains_posts', TRUE)
                ->whereIn('id', $myRubricsIds)
                ->where('rank', '!=', '0')
                ->orderByRaw('position ASC, rank ASC')
                ->get() : collect(),
            'templates' => Post::templates()
                ->with(['rubric', 'author', 'currentUserReader'])
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
                ->leftJoin('rubrics', 'posts.rubric_id', '=', 'rubrics.id')
                ->select('posts.*')
                ->where(function($query) use ($myRubricsIds) {
                    $query->whereIn('posts.rubric_id', $myRubricsIds)
                          ->orWhereNull('posts.rubric_id');
                })
                ->orderByRaw('COALESCE(rubrics.name, "000_Global") ASC')
                ->orderBy('posts.title', 'ASC')
                ->get()
        ]);
    }

    public function deleteTemplate($id) {
        $template = Post::templates()->findOrFail($id);
        $template->delete();
        $this->emit('render'); // Rafraîchir le parent PostsManager
    }
}
