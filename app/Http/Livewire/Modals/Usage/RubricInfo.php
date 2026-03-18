<?php

namespace App\Http\Livewire\Modals\Usage;

use Livewire\Component;
use App\Models\Rubric;
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
        return view('livewire.modals.usage.rubric-info', [
            // Consultation : groupes et profils ayant accès à la rubrique
            'readGroups'   => $this->rubric->groups()->get(),
            'readProfiles' => $this->rubric->profiles()->get(),
            // Édition : groupes et profils ayant le droit 'posts' sur cette rubrique
            'editGroups'   => $this->rubric->groupsWithRubricPostsRight()->get(),
            'editProfiles' => $this->rubric->profilesWithRubricPostsRight()->get(),
        ]);
    }
}
