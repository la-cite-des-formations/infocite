<div class="col">
    <div class="input-group">
        <!-- filtre de recherche 'search' -->
        <span class="input-group-text text-secondary material-icons md-18">search</span>
        <input wire:model='filter.search' type="text" class="form-control" placeholder="Rechercher...">
      @if(!$filter['searchOnly'])
        <!-- bouton d'activation du filtre spécifique -->
        <button wire:click='toggleFilter' class="d-flex btn btn-secondary" title="{{ $showFilter ? 'Masquer' : 'Afficher'}} le filtre">
            <span class="material-icons md-18">filter_list</span>
        </button>
      @endif
    </div>
</div>
