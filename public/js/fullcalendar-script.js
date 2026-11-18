/**
 * Gestion du calendrier FullCalendar pour l'agenda Info Cité.
 */
var fcCalendar = null;
var fcTooltipEl = null;
window.currentAgendaView = window.currentAgendaView || 'timeline';

/**
 * Applique la visibilité de la vue courante (timeline ou calendar).
 */
function applyCurrentAgendaView() {
    var timelineView = document.getElementById('agenda-timeline-view');
    var calendarView = document.getElementById('agenda-calendar-view');
    var btnTimeline = document.getElementById('btn-timeline');
    var btnCalendar = document.getElementById('btn-calendar');

    if (!timelineView || !calendarView) return;

    if (window.currentAgendaView === 'calendar') {
        timelineView.style.display = 'none';
        calendarView.style.display = 'block';
        if (btnTimeline) btnTimeline.classList.remove('active');
        if (btnCalendar) btnCalendar.classList.add('active');

        var calendarEl = document.getElementById('fullcalendar-container');
        if (!fcCalendar || (calendarEl && calendarEl.children.length === 0)) {
            initFullCalendar();
        } else {
            setTimeout(function () {
                if (fcCalendar) fcCalendar.updateSize();
            }, 50);
        }
    } else {
        calendarView.style.display = 'none';
        timelineView.style.display = 'block';
        if (btnCalendar) btnCalendar.classList.remove('active');
        if (btnTimeline) btnTimeline.classList.add('active');
    }
}

/**
 * Bascule entre la vue Timeline et la vue Calendrier.
 */
function switchAgendaView(view) {
    window.currentAgendaView = view;
    applyCurrentAgendaView();
}

/**
 * Initialise le calendrier FullCalendar (dayGridMonth).
 */
function initFullCalendar() {
    var calendarEl = document.getElementById('fullcalendar-container');
    if (!calendarEl) return;

    if (typeof FullCalendar === 'undefined') {
        console.warn('FullCalendar library not yet loaded.');
        return;
    }

    // Si une instance précédente existe, la détruire proprement
    if (fcCalendar) {
        try {
            fcCalendar.destroy();
        } catch (e) {
            console.warn(e);
        }
        fcCalendar = null;
    }

    calendarEl.innerHTML = '';

    // Créer l'élément tooltip si inexistant
    if (!fcTooltipEl) {
        fcTooltipEl = document.createElement('div');
        fcTooltipEl.className = 'fc-event-tooltip';
        document.body.appendChild(fcTooltipEl);
    }

    var eventsData = window.agendaCalendarEvents || [];

    fcCalendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        initialView: 'dayGridMonth',
        locale: 'fr',
        height: 'auto',
        firstDay: 1,
        fixedWeekCount: false,
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'today'
        },
        buttonText: {
            today: "Aujourd'hui"
        },
        events: eventsData,
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            if (window.Livewire) {
                window.Livewire.emitTo('modal-manager', 'show', {
                    component: 'usage.event-detail',
                    data: { eventId: info.event.id }
                });
            }
        },
        eventMouseEnter: function (info) {
            var props = info.event.extendedProps || {};
            var color = info.event.backgroundColor || '#3498db';
            var html = '<div class="tooltip-type" style="background-color: ' + color + ';">' + (props.type || '') + '</div>';
            html += '<div class="fw-bold mt-1">' + info.event.title + '</div>';
            if (props.location) {
                html += '<div class="tooltip-location mt-1"><i class="bx bx-map me-1"></i>' + props.location + '</div>';
            }

            fcTooltipEl.innerHTML = html;
            fcTooltipEl.classList.add('visible');

            var rect = info.el.getBoundingClientRect();
            fcTooltipEl.style.left = rect.left + (rect.width / 2) - (fcTooltipEl.offsetWidth / 2) + 'px';
            fcTooltipEl.style.top = (rect.top + window.scrollY - fcTooltipEl.offsetHeight - 8) + 'px';
        },
        eventMouseLeave: function (info) {
            if (fcTooltipEl) {
                fcTooltipEl.classList.remove('visible');
            }
        }
    });

    fcCalendar.render();
    window.fcCalendar = fcCalendar;
}

/**
 * Écouteur pour la mise à jour dynamique des événements (changement de filtres).
 */
window.addEventListener('agenda-events-updated', function (event) {
    window.agendaCalendarEvents = event.detail.events;
    if (window.fcCalendar) {
        window.fcCalendar.removeAllEvents();
        window.fcCalendar.addEventSource(window.agendaCalendarEvents);
    }
});

/**
 * Enregistrement du hook Livewire pour préserver la vue active lors des requêtes Livewire.
 */
function registerLivewireAgendaHook() {
    if (window.Livewire && window.Livewire.hook) {
        window.Livewire.hook('message.processed', function () {
            applyCurrentAgendaView();
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', registerLivewireAgendaHook);
} else {
    registerLivewireAgendaHook();
}
document.addEventListener('livewire:load', registerLivewireAgendaHook);
