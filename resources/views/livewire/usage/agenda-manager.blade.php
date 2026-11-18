<section id="lw-agenda" class="services section-bg">

    <div class="container d-flex justify-content-center align-items-start position-relative">

        {{-- Boutons de bascule Timeline / Calendrier (immédiat côté client) --}}
        <div class="btn-group shadow-sm position-absolute start-0" role="group" style="z-index: 2;"
            aria-label="Basculer entre Timeline et Calendrier">
            <button type="button" class="d-flex align-items-center btn btn-sm btn-outline-primary active"
                id="btn-timeline" onclick="switchAgendaView('timeline')">
                <i class="bx bx-list-ul me-1 align-middle"></i> Timeline
            </button>
            <button type="button" class="d-flex align-items-center btn btn-sm btn-outline-primary" id="btn-calendar"
                onclick="switchAgendaView('calendar')">
                <i class="bx bx-calendar me-1 align-middle"></i> Calendrier
            </button>
        </div>

        {{-- Titre et description de la rubrique (centrés comme les autres rubriques, sans icône) --}}
        <div class="section-title w-100" style="padding-left: 100px; padding-right: 100px;">
            <h2 class="text-center mb-2">{{ $agendaRubric->title ?? 'Agenda' }}</h2>
            <p class="text-center mb-0 text-muted">
                {{ $agendaRubric->description ?? 'Retrouvez la liste chronologique des événements et manifestations à venir.' }}
            </p>
        </div>

        {{-- Groupe de boutons d'actions en haut à droite, aligné sur la même ligne que le titre --}}
        @can('create', App\Models\Event::class)
            <div class="btn-group btn-group-sm position-absolute end-0 top-0" role="group" style="z-index: 2;"
                aria-label="Actions Agenda">
                <button type="button" class="d-flex align-items-center btn btn-sm btn-primary"
                    wire:click="$emitTo('modal-manager', 'show', {component: 'usage.import-events-csv'})"
                    title="Importer des événements en masse depuis un fichier CSV">
                    <span class="material-icons fs-5">file_download</span>
                </button>
                <a href="{{ route('event.create') }}" class="d-flex align-items-center btn btn-sm btn-success"
                    title="Ajouter un nouvel événement à l'agenda">
                    <span class="material-icons fs-5">add</span>
                </a>
            </div>
        @endcan
    </div>

    <div class="container mx-auto mt-1" @if ($firstLoad) data-aos="fade-up" @endif>
        {{-- Message flash de succès / info --}}
        @includeWhen(session()->has('message'), 'includes.confirm-message')

        <div class="row g-4 justify-content-center align-items-start">
            {{-- ========== COLONNE GAUCHE : FILTRES ========== --}}
            <div class="col-12 col-lg-3">
                <div class="card shadow-sm border-0" style="border-radius: 12px;">
                    <div class="card-body p-3 p-xl-4">
                        <div
                            class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 border-bottom pb-2">
                            <span class="fw-bold text-dark">
                                <i class="bx bx-filter-alt text-primary me-1 align-middle fs-5"></i> Filtrer les
                                événements
                            </span>
                            <button type="button" wire:click="resetFilters"
                                class="btn btn-sm btn-link text-decoration-none text-muted p-0">
                                <i class="bx bx-reset align-middle"></i> Réinitialiser
                            </button>
                        </div>

                        <div class="row g-3">
                            {{-- Type d'événement --}}
                            <div class="col-12">
                                <label for="filterType" class="form-label small fw-bold text-secondary mb-1">Type
                                    d'événement</label>
                                <select id="filterType" wire:model="filterType" class="form-select form-select-sm">
                                    <option value="">Tous les types</option>
                                    @foreach ($eventTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Date de début --}}
                            <div class="col-6 col-lg-12">
                                <label for="filterStartDate" class="form-label small fw-bold text-secondary mb-1">Du
                                    (début)</label>
                                <input type="date" id="filterStartDate" wire:model="filterStartDate"
                                    class="form-control form-control-sm">
                            </div>

                            {{-- Date de fin --}}
                            <div class="col-6 col-lg-12">
                                <label for="filterEndDate" class="form-label small fw-bold text-secondary mb-1">Au
                                    (fin)</label>
                                <input type="date" id="filterEndDate" wire:model="filterEndDate"
                                    class="form-control form-control-sm">
                            </div>

                            {{-- Mot-clé --}}
                            <div class="col-12">
                                <label for="filterSearch" class="form-label small fw-bold text-secondary mb-1">Recherche
                                    par
                                    mot-clé</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="bx bx-search text-muted"></i></span>
                                    <input type="text" id="filterSearch" wire:model.debounce.400ms="filterSearch"
                                        class="form-control" placeholder="Titre, description, lieu...">
                                    @if (!empty($filterSearch))
                                        <button class="btn btn-outline-secondary" type="button"
                                            wire:click="$set('filterSearch', '')">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========== COLONNE DROITE : AGENDA (TIMELINE & CALENDRIER) ========== --}}
            <div class="col-12 col-lg-9">
                {{-- ========== VUE TIMELINE ========== --}}
                <div id="agenda-timeline-view">
                    @if ($events->isEmpty())
                        <div class="alert alert-info text-center shadow-sm py-4">
                            <i class="bx bx-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                            <strong>Aucun événement correspondant</strong>
                            <p class="text-muted mb-0 mt-1">Modifiez vos critères de recherche ou réinitialisez les
                                filtres pour
                                afficher les manifestations.</p>
                        </div>
                    @else
                        <div class="timeline-vertical w-100 m-0" style="max-width: 100%;">
                            @foreach ($events as $event)
                                @php
                                    $thumbnail = $event->getThumbnailUrl();
                                    $focalStyle = $event->focalPointStyle();

                                    // Formatage complet de la date et heure
                                    $formattedDate = \Carbon\Carbon::parse($event->start_date)->translatedFormat(
                                        'l j F Y',
                                    );
                                    if ($event->start_time) {
                                        $formattedDate .=
                                            ($event->end_date == $event->start_date && $event->end_time
                                                ? ' de '
                                                : ' à ') . substr($event->start_time, 0, 5);
                                    }
                                    if ($event->end_date) {
                                        $endDateFormatted = \Carbon\Carbon::parse($event->end_date)->translatedFormat(
                                            'l j F Y',
                                        );
                                        if ($event->end_date != $event->start_date) {
                                            $formattedDate = 'Du ' . $formattedDate . ' au ' . $endDateFormatted;
                                        }
                                        if ($event->end_time) {
                                            $formattedDate .= ' à ' . substr($event->end_time, 0, 5);
                                        }
                                    }

                                    $typeColor = $event->eventType->color ?? '#3498db';
                                @endphp

                                <div class="timeline-item pe-1" wire:key="event-card-{{ $event->id }}">
                                    <!-- Point coloré représentant le type d'événement -->
                                    <div class="timeline-icon-dot shadow-sm"
                                        style="background-color: {{ $typeColor }}; box-shadow: 0 0 0 4px {{ $typeColor }}33 !important;">
                                    </div>

                                    <div class="timeline-card shadow-sm border-0 animate-hover">
                                        <div class="timeline-card-body d-flex flex-column flex-md-row gap-3">
                                            @if ($thumbnail)
                                                <div class="timeline-thumb rounded-3"
                                                    style="background: url('{{ $thumbnail }}') no-repeat; background-size: cover; {{ $focalStyle }} width: 120px; height: 120px; flex-shrink: 0; cursor: pointer;"
                                                    wire:click="$emitTo('modal-manager', 'show', {component: 'usage.event-detail', data: {eventId: {{ $event->id }}}})">
                                                </div>
                                            @endif
                                            <div class="timeline-card-content flex-grow-1">
                                                <div
                                                    class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                                        <span
                                                            class="timeline-date-badge px-2 py-1 rounded-pill small fw-bold text-primary"
                                                            style="background-color: #e8f4fd;">
                                                            <i
                                                                class="bx bx-calendar me-1 align-middle"></i>{{ ucfirst($formattedDate) }}
                                                        </span>
                                                        <span
                                                            class="timeline-type-tag text-white px-2 py-1 rounded small fw-bold"
                                                            style="background-color: {{ $typeColor }};">
                                                            {{ $event->eventType->name ?? 'Événement' }}
                                                        </span>
                                                    </div>

                                                    {{-- Actions d'administration si autorisé --}}
                                                    @can('update', $event)
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('event.edit', ['event_id' => $event->id]) }}"
                                                                class="btn btn-outline-success btn-sm"
                                                                title="Modifier cet événement">
                                                                <i class="bx bx-pencil"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                                wire:click="$emitTo('modal-manager', 'show', {component: 'usage.confirm', data: {handling: 'default', content: 'Voulez-vous vraiment supprimer cet événement de l\'agenda ?', event: 'deleteEvent', params: [{{ $event->id }}]}})"
                                                                title="Supprimer cet événement">
                                                                <i class="bx bx-trash"></i>
                                                            </button>
                                                        </div>
                                                    @endcan
                                                </div>

                                                <h4 class="timeline-card-title fw-bold text-dark mb-2"
                                                    style="cursor: pointer;"
                                                    wire:click="$emitTo('modal-manager', 'show', {component: 'usage.event-detail', data: {eventId: {{ $event->id }}}})"
                                                    title="Cliquer pour voir le détail">
                                                    {{ $event->title }}
                                                </h4>
                                                <p class="timeline-card-desc text-muted mb-0">
                                                    {!! nl2br(e(AP::strLimiter($event->description, 220))) !!}
                                                </p>
                                            </div>
                                        </div>
                                        <div
                                            class="timeline-card-footer d-flex flex-wrap justify-content-between align-items-center mt-3 pt-3 border-top border-light">
                                            @if ($event->location)
                                                <span class="timeline-location text-secondary small">
                                                    <i class="bx bx-map me-1 text-danger align-middle fs-5"></i>
                                                    {{ $event->location }}
                                                </span>
                                            @else
                                                <span></span>
                                            @endif

                                            <div class="d-flex align-items-center gap-2">
                                                <button type="button"
                                                    class="btn btn-sm btn-link text-decoration-none text-muted px-2"
                                                    wire:click="$emitTo('modal-manager', 'show', {component: 'usage.event-detail', data: {eventId: {{ $event->id }}}})">
                                                    <i class="bx bx-info-circle me-1 align-middle"></i> Détails
                                                </button>

                                                @if ($event->post_id && $event->post && $event->post->rubric)
                                                    <a href="{{ $event->post->route }}"
                                                        class="timeline-link btn btn-sm btn-outline-primary rounded-pill px-3">
                                                        Voir l'article <i
                                                            class="bx bx-right-arrow-alt align-middle ms-1"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ========== VUE CALENDRIER (FullCalendar) ========== --}}
                <div id="agenda-calendar-view" style="display: none;">
                    <div id="fullcalendar-container" wire:ignore class="mx-auto" style="max-width: 950px;"></div>
                </div>
            </div>
        </div>
    </div>
    {{-- Données JSON des événements pour FullCalendar --}}
    <script>
        window.agendaCalendarEvents = {!! $calendarEventsJson !!};
        if (typeof applyCurrentAgendaView === 'function') {
            applyCurrentAgendaView();
        }
    </script>

    <style>
        /* ===== Toggle buttons ===== */
        .agenda-toggle-btn {
            padding: 8px 22px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.25s ease;
            border-radius: 0;
        }

        .agenda-toggle-btn:first-child {
            border-radius: 8px 0 0 8px;
        }

        .agenda-toggle-btn:last-child {
            border-radius: 0 8px 8px 0;
        }

        .agenda-toggle-btn.active {
            background-color: var(--bs-primary, #0d6efd);
            color: #fff;
            border-color: var(--bs-primary, #0d6efd);
        }

        /* ===== Timeline styles ===== */
        .timeline-vertical {
            position: relative;
            max-width: 850px;
            margin: 30px auto;
            padding: 10px 0;
        }

        .timeline-vertical::after {
            content: '';
            position: absolute;
            width: 4px;
            background-color: #dee2e6;
            top: 0;
            bottom: 0;
            left: 20px;
            margin-left: -2px;
            border-radius: 2px;
        }

        .timeline-item {
            padding: 10px 15px 30px 50px;
            position: relative;
            background-color: inherit;
            width: auto;
        }

        .timeline-icon-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            position: absolute;
            left: 10px;
            top: 25px;
            z-index: 2;
            border: 4px solid #fff;
        }

        .timeline-card {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 1px solid rgba(0, 0, 0, .05) !important;
        }

        .timeline-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
        }

        /* ===== FullCalendar customizations ===== */
        #fullcalendar-container {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 1.5rem !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Colonnes latérales équilibrées pour centrer rigoureusement le titre */
        .fc .fc-toolbar-chunk:first-child,
        .fc .fc-toolbar-chunk:last-child {
            flex: 1 1 0%;
        }

        .fc .fc-toolbar-chunk:first-child {
            text-align: left;
        }

        .fc .fc-toolbar-chunk:last-child {
            text-align: right;
        }

        .fc .fc-toolbar-chunk:nth-child(2) {
            flex: 2 1 auto;
            text-align: center;
        }

        .fc .fc-toolbar-title {
            font-size: 1.35rem !important;
            font-weight: 700;
            text-transform: capitalize;
            text-align: center;
            margin: 0 !important;
        }

        .fc .fc-button {
            font-size: 0.85rem;
            font-weight: 600;
        }

        .fc .fc-button-primary {
            background-color: var(--bs-primary, #0d6efd);
            border-color: var(--bs-primary, #0d6efd);
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active,
        .fc .fc-button-primary:not(:disabled):active {
            background-color: var(--bs-primary, #0d6efd);
            border-color: var(--bs-primary, #0d6efd);
            opacity: 0.85;
        }

        /* Regroupement des boutons de navigation (prev / next) sans arrondis à leur jonction */
        .fc .fc-button-group {
            display: inline-flex !important;
            vertical-align: middle;
        }

        .fc .fc-button-group>.fc-button {
            border-radius: 0 !important;
            margin: 0 !important;
        }

        .fc .fc-button-group>.fc-button:first-child {
            border-top-left-radius: 6px !important;
            border-bottom-left-radius: 6px !important;
        }

        .fc .fc-button-group>.fc-button:last-child {
            border-top-right-radius: 6px !important;
            border-bottom-right-radius: 6px !important;
        }

        /* Boutons chevrons moins larges et plus harmonieux */
        .fc .fc-prev-button,
        .fc .fc-next-button {
            padding: 5px 8px !important;
            width: 36px !important;
            min-width: 36px !important;
        }

        /* Bouton Aujourd'hui positionné à droite avec ses propres arrondis */
        .fc .fc-today-button {
            border-radius: 6px !important;
            padding: 6px 14px !important;
        }

        .fc .fc-daygrid-day-number {
            font-weight: 600;
            color: #334155;
        }

        .fc .fc-daygrid-day.fc-day-today {
            background-color: rgba(13, 110, 253, 0.06) !important;
        }

        .fc .fc-daygrid-event {
            border-radius: 5px !important;
            padding: 2px 5px;
            font-size: 0.8rem;
            font-weight: 600;
            border: none !important;
            cursor: pointer;
        }

        .fc .fc-col-header-cell-cushion {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            color: #64748b;
        }

        /* FullCalendar tooltip */
        .fc-event-tooltip {
            position: absolute;
            background: #1e293b;
            color: #fff;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.82rem;
            line-height: 1.4;
            max-width: 280px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18);
            z-index: 10000;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.15s ease;
        }

        .fc-event-tooltip.visible {
            opacity: 1;
        }

        .fc-event-tooltip .tooltip-type {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 0.72rem;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .fc-event-tooltip .tooltip-location {
            color: #94a3b8;
            font-size: 0.78rem;
        }
    </style>
</section>
