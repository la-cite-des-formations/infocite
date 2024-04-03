<div wire:init='drawOrgChart'>
    <section id="breadcrumbs" class="breadcrumbs my-4">
    </section>

    <section id="org-chart" class="services section-bg">
        <div class="container d-flex flex-column">
            <div class="align-self-end">
                <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                  @can('access-dashboard', 'org-chart')
                    <a href="dashboard/org-chart" target="_blank" role="button" class="btn btn-success" title="Gérer l'organigramme">
                        <i class="material-icons md-18 align-middle">edit</i>
                    </a>
                  @endcan
                    <button class="btn btn-secondary"
                        title="Recharger l'organigramme"
                        wire:click="drawOrgChart" type="button">
                    <i class="material-icons md-18 align-middle me-1">refresh</i>
                </button>
                </div>
            </div>
        </div>
        <div class="m-3" @if ($firstLoad) data-aos="fade-up" @endif>
            <div class="section-title pb-2">
                <h2 class="title-icon"><i class="material-icons md-36 me-2">{{ $rubric->icon }}</i>{{ $rubric->title }}</h2>
                <p>{{ $rubric->description }}</p>
            </div>
            <div id="orgchart" class="overflow-auto"></div>
        </div>
    </section>
</div>
