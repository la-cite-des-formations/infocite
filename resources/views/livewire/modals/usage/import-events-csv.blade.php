@extends('layouts.modal')

@section('modal-title', 'Import CSV d\'événements (Google Sheets)')

@section('modal-body')
    @if (!$importFinished)
        <div class="mb-3">
            <p class="text-muted">
                Ce module vous permet de créer rapidement plusieurs événements dans l'agenda en téléversant un fichier <strong>CSV</strong> exporté depuis <strong>Google Sheets</strong> (format UTF-8 avec séparateur virgule).
            </p>

            <div class="alert alert-light border d-flex align-items-center justify-content-between p-3 mb-3">
                <div>
                    <strong class="d-block text-dark"><i class="bx bx-file text-primary me-1"></i> Modèle CSV prêt à l'emploi</strong>
                    <span class="text-muted small">Téléchargez notre fichier modèle pré-formaté pour préparer vos données.</span>
                </div>
                <button type="button" wire:click="downloadTemplate" class="btn btn-sm btn-outline-primary">
                    <i class="bx bx-download me-1"></i> Télécharger le modèle
                </button>
            </div>

            <div class="mb-3">
                <label for="csvFileInput" class="form-label fw-bold">Sélectionner le fichier CSV</label>
                <input type="file" id="csvFileInput" wire:model="csvFile" accept=".csv,text/csv,text/plain"
                    class="form-control @error('csvFile') is-invalid @enderror">
                @error('csvFile')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div wire:loading wire:target="csvFile" class="text-primary small mt-1">
                    <i class="bx bx-loader-alt bx-spin"></i> Chargement du fichier...
                </div>
            </div>

            <div class="card bg-light border-0 small p-3 text-secondary">
                <span class="fw-bold mb-1 text-dark"><i class="bx bx-info-circle me-1"></i> Colonnes prises en charge :</span>
                <code>titre</code>, <code>description</code>, <code>type_evenement</code> (nom ou ID), <code>date_debut</code> (AAAA-MM-JJ ou JJ/MM/AAAA), <code>heure_debut</code>, <code>date_fin</code>, <code>heure_fin</code>, <code>lieu</code>, <code>id_article</code>.
            </div>
        </div>
    @else
        {{-- Compte-rendu / Log d'importation --}}
        <div class="text-center py-2">
            @if ($importedCount > 0)
                <div class="alert alert-success d-flex align-items-center justify-content-center py-3 mb-3">
                    <i class="bx bx-check-circle fs-2 me-2"></i>
                    <div class="text-start">
                        <strong class="fs-6 d-block">{{ $importedCount }} événement(s) créé(s) avec succès !</strong>
                        <span class="small text-muted">{{ $processedTotal }} ligne(s) analysée(s) au total.</span>
                    </div>
                </div>
            @else
                <div class="alert alert-warning py-3 mb-3">
                    <i class="bx bx-error-circle fs-2 d-block mb-1"></i>
                    <strong>Aucun événement n'a pu être importé.</strong>
                    <div class="small">Veuillez vérifier les colonnes et le format de votre fichier.</div>
                </div>
            @endif

            @if (count($errorsList) > 0)
                <div class="text-start mt-3">
                    <h6 class="fw-bold text-danger mb-2">
                        <i class="bx bx-error me-1"></i> Anomalies détectées ({{ count($errorsList) }}) :
                    </h6>
                    <div class="overflow-auto border rounded p-2 bg-light" style="max-height: 180px; font-size: 0.85rem;">
                        <ul class="mb-0 ps-3 text-danger">
                            @foreach ($errorsList as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    @endif
@endsection

@section('modal-footer')
    @if (!$importFinished)
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" wire:click="import" wire:loading.attr="disabled" class="btn btn-primary" @if(!$csvFile) disabled @endif>
            <span wire:loading.remove wire:target="import"><i class="bx bx-upload me-1"></i> Lancer l'import</span>
            <span wire:loading wire:target="import"><i class="bx bx-loader-alt bx-spin me-1"></i> Importation en cours...</span>
        </button>
    @else
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
            Fermer et actualiser l'agenda
        </button>
    @endif
@endsection
