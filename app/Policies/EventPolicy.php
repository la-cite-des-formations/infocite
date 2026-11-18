<?php

namespace App\Policies;

use App\CustomFacades\AP;
use App\Models\Event;
use App\Models\Roles;
use App\Models\Rubric;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Politique d'accès pour le modèle Event (Agenda).
 * Les droits s'appuient sur la rubrique Agenda (ou sa rubrique parente Événements).
 */
class EventPolicy
{
    use HandlesAuthorization;

    /**
     * Identifiant ou instance de la rubrique Agenda.
     *
     * @return Rubric|null
     */
    protected function getAgendaRubric(): ?Rubric
    {
        return Rubric::where('segment', 'agenda')->orWhere('name', 'Agenda')->first();
    }

    /**
     * Détermine si l'utilisateur peut voir la liste des événements.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Détermine si l'utilisateur peut voir un événement spécifique.
     *
     * @param User $user
     * @param Event $event
     * @return bool
     */
    public function view(User $user, Event $event): bool
    {
        return true;
    }

    /**
     * Détermine si l'utilisateur peut créer un événement dans l'agenda.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $rubric = $this->getAgendaRubric();
        if (!$rubric) {
            return $user->hasRole('posts', Roles::IS_EDITR);
        }

        // Vérification de l'accès à la rubrique
        if (!$user->myRubrics()->contains('id', $rubric->id)) {
            return false;
        }

        $rootRubric = $rubric->parent ?? $rubric;
        $canCreate = $user->hasRole('posts', Roles::IS_EDITR, 'Rubric', $rubric->id, AP::STRICTLY);

        return $canCreate ?? $user->hasRole(
            'posts',
            Roles::IS_EDITR,
            'Rubric',
            $rootRubric->id
        ) ?? $user->hasRole('posts', Roles::IS_EDITR);
    }

    /**
     * Détermine si l'utilisateur peut modifier un événement.
     *
     * @param User $user
     * @param Event $event
     * @return bool
     */
    public function update(User $user, Event $event): bool
    {
        $rubric = $this->getAgendaRubric();
        if (!$rubric) {
            return $user->hasRole('posts', Roles::IS_EDITR);
        }

        // Si l'événement est lié à un article, vérifier aussi si l'utilisateur peut modifier l'article
        if ($event->post_id && $event->post) {
            if ($user->can('update', $event->post)) {
                return true;
            }
        }

        $rootRubric = $rubric->parent ?? $rubric;
        return $user->hasRole('posts', Roles::IS_EDITR, 'Rubric', $rubric->id)
            || $user->hasRole('posts', Roles::IS_EDITR, 'Rubric', $rootRubric->id)
            || $user->hasRole('posts', Roles::IS_EDITR);
    }

    /**
     * Détermine si l'utilisateur peut supprimer un événement.
     *
     * @param User $user
     * @param Event $event
     * @return bool
     */
    public function delete(User $user, Event $event): bool
    {
        return $this->update($user, $event);
    }
}
