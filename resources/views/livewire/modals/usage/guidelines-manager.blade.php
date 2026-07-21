@extends('layouts.modal')

@section('modal-title')
    @if ($guideline && $guideline->post)
        <div class="d-flex align-items-center"><span
                class="material-icons me-1 mb-1">{{ $guideline->post->rubric->icon ?? 'help_outline' }}</span>
            {{ $guideline->post->rubric->title }}</div>
    @else
        <div class="d-flex align-items-center"><span class="material-icons me-1 mb-1">help_outline</span>
            Guide en ligne</div>
    @endif
@endsection

@section('modal-body')
    @if ($guideline && $guideline->post)
        {{-- Déclenchement de la mise en surbrillance CSS si un sélecteur est configuré --}}
        @if ($cssSelector)
            <div x-data x-init="$nextTick(() => {
                window.dispatchEvent(new CustomEvent('guideline-highlight', {
                    detail: { selector: @js($cssSelector) }
                }));
            });">
            </div>
        @endif
        <h3><span class="material-icons">{{ $guideline->post->icon ?? 'help_outline' }}</span>
            {{ $guideline->post->title }}</h3>
        <div class="guideline-post-content">
            {!! $guideline->post->content !!}
        </div>
    @else
        <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
            <span class="material-icons" aria-hidden="true">warning_amber</span>
            <span>Ce guide n'est pas disponible pour le moment.</span>
        </div>
    @endif
@endsection

@section('modal-footer')
    @if ($guideline && $nextContextKey)
        {{-- Parcours guidé : bouton "Étape suivante" --}}
        <button type="button" id="guideline-btn-next" wire:click="openNextStep"
            class="btn btn-primary d-flex align-items-center gap-1">
            Étape suivante
            <span class="material-icons ms-1" style="font-size:18px" aria-hidden="true">arrow_forward</span>
        </button>
    @endif

    <button type="button" id="guideline-btn-cancel" data-bs-dismiss="modal" class="btn btn-secondary">
        Fermer
    </button>
@endsection
