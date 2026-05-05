@extends('layouts.modal')

@section('modal-title')
    <div class="d-flex align-items-center">
        <i class="material-icons-outlined me-2">history_edu</i>
        Gestion des modèles d'articles
    </div>
@endsection

@section('modal-size', 'modal-lg')

@section('modal-body')
    @if($isUne)
        <div class="mb-3">
            <label for="template-filter" class="form-label text-muted small fw-bold mb-1">Filtrer par destination :</label>
            <select id="template-filter" wire:model="filterRubricId" class="form-select form-select-sm border-secondary border-opacity-25 w-auto">
                <option value="all">Toutes les rubriques</option>
                <option value="global">Modèles globaux (sans rubrique)</option>
                @foreach($allRubrics as $r)
                    @can('create', ['App\\Models\\Post', $r->id])
                        <option value="{{ $r->id }}">{{ (is_object($r->parent) ? $r->parent->name . ' / ' : '') . $r->name }}</option>
                    @endcan
                @endforeach
            </select>
        </div>
    @endif
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Rubrique</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                <tr>
                    <td class="align-middle border-0">
                        <i class="material-icons v-middle me-1">{{ $template->icon }}</i>
                        {{ $template->title }}
                    </td>
                    <td class="align-middle border-0 text-muted small">
                        <span class="badge {{ $template->rubric_id ? 'bg-secondary' : 'bg-primary' }}">
                            {{ $template->rubric ? $template->rubric->name : 'Global' }}
                        </span>
                    </td>
                    <td class="align-middle border-0 text-end">
                        <a href="{{ route('post.edit', ['rubric' => ($template->rubric ? $template->rubric->segmentPath() : 'site.test-modeles'), 'post_id' => $template->id]) }}" 
                           class="btn btn-sm btn-primary" title="Modifier le modèle">
                            <i class="bx bx-pencil"></i>
                        </a>
                        <button wire:click="deleteTemplate({{ $template->id }})" 
                                class="btn btn-sm btn-danger" 
                                title="Supprimer le modèle"
                                onclick="confirm('Confirmer la suppression de ce modèle ?') || event.stopImmediatePropagation()">
                            <i class="bx bx-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-4 text-muted fst-italic">
                        Aucun modèle disponible.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('modal-footer')
    <a href="{{ route('post.create', ['rubric' => $targetCreateRoute, 'template' => 1]) }}"
       class="btn btn-success me-auto" title="Créer un nouveau modèle">
        <span class="material-icons align-middle" style="font-size:18px">add</span> Nouveau modèle
    </a>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
@endsection
