<div class="col">
    <div class="input-group">
        <!-- filtre de recherche 'search' -->
        <div class="input-group-prepend">
            <div class="input-group-text text-secondary">
                <span class="material-icons md-18">search</span>
            </div>
        </div>
        <input wire:model='filter.search' type="text" class="form-control" placeholder="Rechercher...">
      @if(count($filter) > 1)
        <!-- bouton d'activation du filtre spécifique -->
        <div class="input-group-append">
            <button wire:click='toggleFilter' class="btn btn-sm btn-secondary" title="{{ $showFilter ? 'Masquer' : 'Afficher'}} le filtre">
                <span class="material-icons md-18">filter_list</span>
            </button>
        </div>
      @endif
    </div>
</div>
