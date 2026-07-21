{{--
    Composant Blade réutilisable : Bouton d'aide contextuel (icône '?')
    Déclenche l'ouverture de la modale du guide en ligne correspondant au contexte donné.

    Paramètres :
    - $contextKey (string, obligatoire) : Clé du contexte du guide à afficher.
    - $label      (string, optionnel)  : Libellé accessible pour les lecteurs d'écran.

    Utilisation :
        <x-help-button context-key="desktop-notifications" />
        <x-help-button context-key="post-edition" label="Aide sur l'édition d'articles" />
--}}
@props([
    'contextKey',
    'label' => "Afficher l'aide",
])

<button
    type="button"
    id="help-btn-{{ $contextKey }}"
    aria-label="{{ $label }}"
    title="{{ $label }}"
    wire:click="$emitTo('modal-manager', 'show', { component: 'usage.guideline-post-modal', data: { contextKey: '{{ $contextKey }}' } })"
    class="btn btn-sm btn-outline-secondary help-button d-inline-flex align-items-center justify-content-center"
    style="width: 28px; height: 28px; border-radius: 50%; padding: 0; font-weight: bold;"
>
    <span class="material-icons" style="font-size: 16px;" aria-hidden="true">help_outline</span>
</button>
