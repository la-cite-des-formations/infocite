/**
 * Bascule entre la vue Timeline et la vue Calendrier FullCalendar.
 */
var fcCalendar = null;
var fcTooltipEl = null;

function switchAgendaView(view) {
    var timelineView = document.getElementById('agenda-timeline-view');
    var calendarView = document.getElementById('agenda-calendar-view');
    var btnTimeline = document.getElementById('btn-timeline');
    var btnCalendar = document.getElementById('btn-calendar');

    if (view === 'calendar') {
        timelineView.style.display = 'none';
        calendarView.style.display = 'block';
        btnTimeline.classList.remove('active');
        btnCalendar.classList.add('active');

        // Initialiser FullCalendar au premier affichage
        if (!fcCalendar) {
            initFullCalendar();
        } else {
            // Forcer le re-rendu quand on revient sur le calendrier
            fcCalendar.updateSize();
        }
    } else {
        calendarView.style.display = 'none';
        timelineView.style.display = 'block';
        btnCalendar.classList.remove('active');
        btnTimeline.classList.add('active');
    }
}

/**
 * Initialise le calendrier FullCalendar (dayGridMonth).
 */
function initFullCalendar() {
    var calendarEl = document.getElementById('fullcalendar-container');

    // Créer l'élément tooltip
    fcTooltipEl = document.createElement('div');
    fcTooltipEl.className = 'fc-event-tooltip';
    document.body.appendChild(fcTooltipEl);

    fcCalendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        initialView: 'dayGridMonth',
        locale: 'fr',
        height: 'auto',
        firstDay: 1,
        fixedWeekCount: false,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        buttonText: {
            today: "Aujourd'hui"
        },
        events: agendaCalendarEvents,
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            if (info.event.url) {
                window.location.href = info.event.url;
            }
        },
        eventMouseEnter: function (info) {
            var props = info.event.extendedProps;
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
            fcTooltipEl.classList.remove('visible');
        }
    });

    fcCalendar.render();
}