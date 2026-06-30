<section id="lw-agenda" class="services section-bg">
    <div class="container" data-aos="fade-up">
        <div class="section-title">
            <h2 class="title-icon">
                <i class="material-icons-outlined md-36 text-primary">calendar_month</i>
                Agenda
            </h2>
            <p>Retrouvez la liste chronologique des événements et manifestations à venir.</p>
        </div>

        {{-- Boutons de bascule Timeline / Calendrier --}}
        <div class="d-flex justify-content-center mb-4">
            <div class="btn-group shadow-sm" role="group" aria-label="Basculer entre Timeline et Calendrier">
                <button type="button" class="btn btn-outline-primary active agenda-toggle-btn" id="btn-timeline" onclick="switchAgendaView('timeline')">
                    <i class="bx bx-list-ul me-1 align-middle"></i> Timeline
                </button>
                <button type="button" class="btn btn-outline-primary agenda-toggle-btn" id="btn-calendar" onclick="switchAgendaView('calendar')">
                    <i class="bx bx-calendar me-1 align-middle"></i> Calendrier
                </button>
            </div>
        </div>

        {{-- ========== VUE TIMELINE ========== --}}
        <div id="agenda-timeline-view">
            @if($events->isEmpty())
                <div class="alert alert-info text-center shadow-sm py-4">
                    <i class="bx bx-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                    <strong>Aucun événement à venir</strong>
                    <p class="text-muted mb-0 mt-1">Revenez plus tard pour consulter les prochains rendez-vous.</p>
                </div>
            @else
                <div class="timeline-vertical">
                    @foreach ($events as $event)
                        @php
                            $hasGallery = $event->post->gallery && count($event->post->gallery->images ?? []) > 0;
                            $thumbnail = null;
                            if ($hasGallery) {
                                $firstImg = $event->post->gallery->sortedImages()->first();
                                $thumbnail = $firstImg ? $firstImg['path'] : null;
                            }
                            
                            // Date formatting
                            $formattedDate = \Carbon\Carbon::parse($event->start_date)->translatedFormat('l j F Y');
                            if ($event->start_time) {
                                $formattedDate .= ' à ' . substr($event->start_time, 0, 5);
                            }
                            if ($event->end_date) {
                                $endDateFormatted = \Carbon\Carbon::parse($event->end_date)->translatedFormat('l j F Y');
                                if ($event->end_date != $event->start_date) {
                                    $formattedDate .= ' au ' . $endDateFormatted;
                                }
                                if ($event->end_time) {
                                    $formattedDate .= ' à ' . substr($event->end_time, 0, 5);
                                }
                            }
                            
                            $typeColor = $event->eventType->color ?? '#3498db';
                        @endphp
                        
                        <div class="timeline-item">
                            <!-- Colored point representing event type -->
                            <div class="timeline-icon-dot shadow-sm" style="background-color: {{ $typeColor }}; box-shadow: 0 0 0 4px {{ $typeColor }}33 !important;"></div>
                            
                            <div class="timeline-card shadow-sm border-0 animate-hover">
                                <div class="timeline-card-body d-flex flex-column flex-md-row gap-3">
                                    @if($thumbnail)
                                        <div class="timeline-thumb rounded-3" style="background: url('{{ $thumbnail }}') no-repeat center center; background-size: cover; width: 120px; height: 120px; flex-shrink: 0;"></div>
                                    @endif
                                    <div class="timeline-card-content flex-grow-1">
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                            <span class="timeline-date-badge px-2 py-1 rounded-pill small fw-bold text-primary" style="background-color: #e8f4fd;">
                                                <i class="bx bx-calendar me-1 align-middle"></i>{{ ucfirst($formattedDate) }}
                                            </span>
                                            <span class="timeline-type-tag text-white px-2 py-1 rounded small fw-bold" style="background-color: {{ $typeColor }};">
                                                {{ $event->eventType->name }}
                                            </span>
                                        </div>
                                        <h4 class="timeline-card-title fw-bold text-dark mb-2">{{ $event->post->title }}</h4>
                                        <p class="timeline-card-desc text-muted mb-0">
                                            {!! AP::strLimiter(strip_tags($event->post->content), 180) !!}
                                        </p>
                                    </div>
                                </div>
                                <div class="timeline-card-footer d-flex flex-wrap justify-content-between align-items-center mt-3 pt-3 border-top border-light">
                                    @if($event->location)
                                        <span class="timeline-location text-secondary small">
                                            <i class="bx bx-map me-1 text-danger align-middle fs-5"></i>
                                            {{ $event->location }}
                                        </span>
                                    @else
                                        <span></span>
                                    @endif
                                    <a href="{{ $event->post->route }}" class="timeline-link btn btn-sm btn-outline-primary rounded-pill px-3">
                                        Voir l'article <i class="bx bx-right-arrow-alt align-middle ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ========== VUE CALENDRIER (FullCalendar) ========== --}}
        <div id="agenda-calendar-view" style="display: none;">
            <div id="fullcalendar-container" class="mx-auto" style="max-width: 950px;"></div>
        </div>
    </div>

    {{-- Données JSON des événements pour FullCalendar --}}
    <script>
        var agendaCalendarEvents = {!! $calendarEventsJson !!};
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
            border: 1px solid rgba(0,0,0,.03) !important;
        }
        .timeline-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
        }
        .animate-hover {
            transition: all 0.2s ease-in-out;
        }
        .timeline-date-badge {
            display: inline-flex;
            align-items: center;
        }
        .timeline-type-tag {
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        .timeline-card-title {
            font-size: 1.25rem;
        }
        @media (max-width: 768px) {
            .timeline-thumb {
                width: 100% !important;
                height: 150px !important;
                margin-bottom: 10px;
            }
        }

        /* ===== FullCalendar customizations ===== */
        #fullcalendar-container {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }
        .fc .fc-toolbar-title {
            font-size: 1.3rem !important;
            font-weight: 700;
            text-transform: capitalize;
        }
        .fc .fc-button {
            font-size: 0.85rem;
            padding: 6px 14px;
            border-radius: 6px !important;
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
            box-shadow: 0 6px 16px rgba(0,0,0,0.18);
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
