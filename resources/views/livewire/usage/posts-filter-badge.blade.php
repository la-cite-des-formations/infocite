<div class="row">
    <div class="col-12">
        <div class="d-flex align-items-center px-2">
            @if (Session::get('lastFilter'))
                <i
                    class="material-icons text-primary fs-4 ms-0 me-2">{{ AP::getUneFilteredByName(Session::get('lastFilter'))['icone'] }}</i>
                <h3 class="h5 mb-0 fw-bold text-dark">
                    {{ AP::getUneFilteredByName(Session::get('lastFilter'))['libelle'] }}</h3>
            @elseif(Session::get('lastSorter'))
                <i
                    class="material-icons text-info fs-4 ms-0 me-2">{{ AP::getUneSortedByName(Session::get('lastSorter'))['icone'] }}</i>
                <h3 class="h5 mb-0 fw-bold text-dark">{{ AP::getUneSortedByName(Session::get('lastSorter'))['libelle'] }}
                </h3>
            @else
                <i class="material-icons text-secondary fs-4 ms-0 me-2">grid_view</i>
                <h3 class="h5 mb-0 fw-bold text-dark">Tous les articles</h3>
            @endif
        </div>
    </div>
</div>
