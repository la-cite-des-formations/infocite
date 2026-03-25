<?php

namespace App\Http\Livewire\Modals\Usage;

use Livewire\Component;
use App\Models\Rubric;
use App\Models\Right;
use App\Models\Roles;

/**
 * Composant Livewire affichant les droits (consultation et édition) d'une rubrique.
 * Utilisé via la modale déclenchée par le bouton « Info » sur l'interface publique.
 */
class RubricInfo extends Component
{
    public Rubric $rubric;

    /**
     * Initialisation : récupère la rubrique depuis les données transmises par la modale.
     *
     * @param array $data Données contenant l'id de la rubrique.
     */
    public function mount($data)
    {
        $this->rubric = Rubric::find($data['id']);
    }

    /**
     * Rendu du composant avec les listes de consultation et d'édition.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $postsRight = Right::where('name', 'posts')->first();
        $rubricsRight = Right::where('name', 'rubrics')->first();

        // IDs à vérifier (héritage parent-enfant)
        $ids = [$this->rubric->id];
        if ($this->rubric->parent_id) {
            $ids[] = $this->rubric->parent_id;
        }

        // Masques binaires
        $editorRolesMask = Roles::IS_EDITR | Roles::IS_MODER | Roles::IS_ADMIN;
        $readerRolesMask = Roles::IS_READR;

        /**
         * Fonction de récupération des entités autorisées via le système de droits.
         */
        $getEntities = function($right, $relation, $mask) use ($ids) {
            if (!$right) return collect();
            return $relation
                ->where(function ($query) use ($ids) {
                    // 1. Droit spécifique à la rubrique ou sa parente
                    $query->where(function ($q) use ($ids) {
                        $q->where('resource_type', 'Rubric')
                          ->whereIn('resource_id', $ids);
                    })
                    // 2. Droit générique (Global ou spécifique à toutes les rubriques)
                    ->orWhere(function ($q) {
                        $q->whereNull('resource_id')
                          ->where(function ($qq) {
                              $qq->whereNull('resource_type')
                                ->orWhere('resource_type', 'Rubric');
                          });
                    });
                })
                ->whereRaw('roles & ?', [$mask])
                ->orderByDesc('priority')
                ->get();
        };

        return view('livewire.modals.usage.rubric-info', [
            // Consultation : Groupes rattachés (group_rubric) + Entités ayant le droit 'rubrics' (Lecteur)
            'readGroups' => $this->rubric->groups()->get()
                ->merge($getEntities($rubricsRight, $rubricsRight->groups(), $readerRolesMask))
                ->unique('id'),
            'readProfiles' => $getEntities($rubricsRight, $rubricsRight->profiles(), $readerRolesMask),
            'readUsers'    => $getEntities($rubricsRight, $rubricsRight->realUsers(), $readerRolesMask),

            // Édition : Entités ayant le droit 'posts' (Éditeur/Moder/Admin)
            'editGroups'   => $getEntities($postsRight, $postsRight->groups(), $editorRolesMask),
            'editProfiles' => $getEntities($postsRight, $postsRight->profiles(), $editorRolesMask),
            'editUsers'    => $getEntities($postsRight, $postsRight->realUsers(), $editorRolesMask),
        ]);
    }
}
