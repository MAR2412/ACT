<div>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <style>
        /* Opacidad de las líneas de la cuadrícula */
        .fc .fc-scrollgrid,
        .fc .fc-col-header-cell,
        .fc .fc-daygrid-day {
            border-color: rgba(147, 160, 179, 0.15) !important;
            /* slate-500 con opacidad baja */
        }

        .fc .fc-scrollgrid td:last-child,
        .fc .fc-scrollgrid th:last-child {
            border-color: rgba(147, 160, 179, 0.15) !important;
        }

        .fc .fc-scrollgrid tr:last-child td {
            border-color: rgba(147, 160, 179, 0.15) !important;
        }

        .fc .fc-toolbar-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #44403c;
        }

        .dark .fc .fc-toolbar-title {
            color: #15d4faff;
        }

        .fc .fc-button {
            background-color: #15b1faff;
            color: #44403c;
            font-weight: 600;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            border: none;
            transition: background 0.2s;
        }

        .fc .fc-button:hover,
        .fc .fc-button-active {
            background-color: rgba(11, 245, 116, 1);
            color: #ffffffff;
        }

        .dark .fc .fc-button {
            background-color: #1c1917;
            color: #15fa24ff;
        }

        .dark .fc .fc-button:hover,
        .dark .fc .fc-button-active {
            background-color: #50C877;
            color: #1c1917;
        }

        .fc .fc-daygrid-day-number {
            font-size: 1rem;
            font-weight: 600;
            color: #1c1917;
        }

        .dark .fc .fc-daygrid-day-number {
            color: #50C878;
        }

        .fc .fc-daygrid-day {
            transition: background 0.2s;
        }

        .fc .fc-day-today {
            background-color: #c3fee5ff;
        }

        .dark .fc .fc-day-today {
            background-color: #9adfccff;
        }

        .fc .fc-col-header-cell {
            background-color: #f3f4f6;
            color: #36332f;
        }

        .dark .fc .fc-col-header-cell {
            background-color: #44403c;
            color: #4CAF50;
        }

        .fc .fc-event {
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            padding: 0.25rem 0.5rem;
            font-size: 0.95rem;
            font-weight: 600;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <div class="mx-auto mt-4">
        <div class="rounded-2xl border border-stone-200 bg-white dark:border-stone-700 dark:bg-white/5 p-4">
            <div id="calendar"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [
                    { title: 'Evento 5', start: '2025-08-01', color: '#22d3ee' },
                    { title: 'Evento 5', start: '2025-08-01', color: '#22d3ee' },
                    { title: 'Evento 6', start: '2025-08-07', color: '#10b981' }
                ],
                dayMaxEvents: 4,
                dayPopoverContent: function (arg) {
                    // arg.events contiene los eventos del día
                    let html = `<div class='p-4 bg-white dark:bg-stone-900 rounded-xl shadow-lg w-72'>`;
                    html += `<h3 class='text-lg font-bold text-stone-800 dark:text-green-400 mb-2'>Eventos del día</h3>`;
                    html += `<ul class='space-y-2'>`;
                    arg.events.forEach(event => {
                        html += `<li class='flex items-center gap-2 p-2 rounded-lg bg-stone-100 dark:bg-stone-800'>`;
                        html += `<span class='inline-block w-3 h-3 rounded-full' style='background:${event.backgroundColor || event.color};'></span>`;
                        html += `<span class='font-semibold text-stone-700 dark:text-green-200'>${event.title}</span>`;
                        html += `</li>`;
                    });
                    html += `</ul>`;
                    html += `</div>`;
                    return { html };
                },
                themeSystem: 'standard',
            });
            calendar.render();
            // Actualiza el modo oscuro si cambia
            function updateDarkMode() {
                if (document.documentElement.classList.contains('dark')) {
                    calendarEl.classList.add('dark');
                } else {
                    calendarEl.classList.remove('dark');
                }
            }
            updateDarkMode();
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateDarkMode);
        });
    </script>
    <!-- FullCalendar CSS y JS desde CDN -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>


</div>