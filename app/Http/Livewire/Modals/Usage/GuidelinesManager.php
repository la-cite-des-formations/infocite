<?php

namespace App\Http\Livewire\Modals\Usage;

use App\Models\Guideline;
use Livewire\Component;

/**
 * Composant Livewire pour l'affichage d'un article du "Guide en ligne" dans une modale.
 * Peut être déclenché par un bouton d'aide (icône '?') en passant une context_key,
 * ou automatiquement lors de l'onboarding.
 *
 * Gère également la navigation vers l'étape suivante d'un parcours guidé.
 */
class GuidelinesManager extends Component
{
    /**
     * L'entrée guideline chargée (avec son Post associé).
     *
     * @var \App\Models\Guideline|null
     */
    public ?Guideline $guideline = null;

    /**
     * Clé du contexte du guide suivant (pour les parcours guidés).
     *
     * @var string|null
     */
    public ?string $nextContextKey = null;

    /**
     * Initialisation du composant.
     * Accepte soit une context_key pour résoudre le guideline, soit un id direct.
     *
     * @param array $data Données de la modale : ['contextKey' => '...'] ou ['guidelineId' => ...]
     */
    public function mount($data)
    {
        $contextKey = $data['contextKey'] ?? null;
        $guidelineId = $data['guidelineId'] ?? null;

        if ($contextKey) {
            $this->guideline = Guideline::forContext($contextKey);
        } elseif ($guidelineId) {
            $this->guideline = Guideline::with('post')->find($guidelineId);
        }

        if ($this->guideline) {
            $this->nextContextKey = $this->guideline->next_context_key;

            $this->markAsRead();
        }
    }

    /**
     * Marque l'article de guide comme lu par l'utilisateur courant.
     * Utilisé à la fermeture de la modale pour l'onboarding.
     * Émet également 'guidelinesRead' avec la context_key pour permettre aux
     * composants écouteurs (ex: FCM notifications) de réagir à la lecture.
     */
    public function markAsRead()
    {
        if ($this->guideline?->post && auth()->check()) {
            $user = auth()->user();
            $post = $this->guideline->post;

            // Attache ou met à jour la relation de lecture (post_user)
            if (!$post->readers()->where('user_id', $user->id)->exists()) {
                $post->readers()->attach($user->id, ['is_read' => true]);
            } else {
                $post->readers()->updateExistingPivot($user->id, ['is_read' => true]);
            }
        }

        // Notifie les composants écouteurs (ex: FCM) que ce guide a été lu
        if ($this->guideline) {
            $this->emit('guidelinesRead', $this->guideline->context_key);
        }
    }

    /**
     * Ouvre le guide de l'étape suivante (parcours guidé).
     * Émet un événement pour que le ModalManager charge le prochain guide.
     */
    public function openNextStep()
    {
        $this->markAsRead();

        if ($this->nextContextKey) {
            $this->emitTo('modal-manager', 'show', [
                'component' => 'usage.guidelines-manager',
                'data'      => ['contextKey' => $this->nextContextKey],
            ]);
        }
    }

    /**
     * Rendu du composant.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.modals.usage.guidelines-manager');
    }
}
