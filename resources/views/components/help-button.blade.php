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
@props(['contextKey', 'label' => "Afficher l'aide"])

<button type="button" id="help-btn-{{ $contextKey }}" aria-label="{{ $label }}" title="{{ $label }}"
    onclick="Livewire.emitTo('modal-manager', 'show', { component: 'usage.guidelines-manager', data: { contextKey: '{{ $contextKey }}' } })"
    class="btn help-button p-0 border-0 background-transparent d-inline-flex align-items-center justify-content-center align-self-center ms-1"
    style="vertical-align: middle; margin-top: -3px;">
    <span class="material-icons help-icon"
        style="font-size: 24px; color: var(--select-color-1, #b32428); transition: color 0.2s ease;"
        aria-hidden="true">help_outline</span>
</button>
<style>
    .help-button:hover .help-icon {
        color: var(--select-color-2, #8b1c1f) !important;
    }
</style>
