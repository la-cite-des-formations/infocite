<div wire:init='drawOrgChart'>
    <section id="breadcrumbs" class="breadcrumbs my-4">
    </section>

    <section id="org-chart" class="services section-bg">
      @if($canAdmin)
        <div class="container d-flex flex-column">
            <div class="align-self-end">
                <div class="btn-group btn-group-sm" role="group" aria-label="Administration">
                    <a href="dashboard/org-chart" target="_blank" role="button" class="btn btn-success" title="Gérer les organigrammes">
                        <i class="material-icons md-18 align-middle">edit</i>
                    </a>
                </div>
            </div>
        </div>
      @endif
        <div class="container" @if ($firstLoad) data-aos="fade-up" @endif>
            <div class="section-title pb-2">
                <h2 class="title-icon"><i class="material-icons md-36 me-2">{{ $rubric->icon }}</i>{{ $rubric->title }}</h2>
                <p>{{ $rubric->description }}</p>
            </div>
            <div id="org-chart-draw" class="text-center mb-5">
                <div class="btn-group btn-group-sm" role="group" aria-label="Affichage">
                    <button class="btn btn-primary"
                        title="Afficher l'organigramme procédural"
                        wire:click="drawOrgChart('Process')" type="button">
                        <i class="material-icons md-18 align-middle me-1">developer_board</i>Organigramme procédural
                    </button>
                    <button class="btn btn-warning"
                        title="Afficher l'organigramme relationnel"
                        wire:click="drawOrgChart('Actor')" type="button">
                        <i class="material-icons md-18 align-middle me-1">supervisor_account</i>Organigramme relationnel
                    </button>
                </div>
            </div>
            <h4 class="text-center mb-4">{{ $orgChartTitle }}</h4>
            <div id="orgchart"></div>
        </div>
    </section>
</div>
