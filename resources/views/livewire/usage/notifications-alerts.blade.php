<div>
    @if ($alerts->isNotEmpty())
        <div class="container mt-2 mb-3">
            @foreach ($alerts as $notification)
                @if ($notification->content_type === 'RP')
                    <div wire:key="notification-alert-{{ $notification->id }}" class="alert alert-warning alert-dismissible fade show d-flex align-items-center shadow-sm mb-2" role="alert">
                        <span class="material-icons text-warning-emphasis fs-3 me-3 align-middle">history_toggle_off</span>
                        <div class="flex-grow-1">
                            <strong class="me-1">Obsolescence des contenus :</strong>
                            Vous avez des articles inactifs depuis plus d'un an nécessitant un arbitrage.
                            <a href="/infos#review-posts" class="alert-link text-decoration-underline fw-bold ms-2">
                                Accéder à mes articles à réviser &rarr;
                            </a>
                        </div>
                        <button type="button" class="btn-close" wire:click="dismiss({{ $notification->id }})" aria-label="Fermer" title="Fermer l'alerte"></button>
                    </div>
                @elseif ($notification->content_type === 'DF')
                    <div wire:key="notification-alert-{{ $notification->id }}" class="alert alert-warning alert-dismissible fade show d-flex align-items-center shadow-sm mb-2" role="alert">
                        <span class="material-icons text-warning-emphasis fs-3 me-3 align-middle">lightbulb</span>
                        <div class="flex-grow-1">
                            <strong class="me-1">Modèles d'articles :</strong>
                            Pensez aux modèles prédéfinis pour faciliter votre rédaction et harmoniser la présentation de vos contenus sur l'intranet.
                            <a href="{{ route('templates.reminder') }}" wire:click.prevent="openTemplateReminder({{ $notification->id }})" class="alert-link text-decoration-underline fw-bold ms-2">
                                Accéder à la Une en mode édition &rarr;
                            </a>
                        </div>
                        <button type="button" class="btn-close" wire:click="dismiss({{ $notification->id }})" aria-label="Fermer" title="Fermer l'alerte"></button>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
