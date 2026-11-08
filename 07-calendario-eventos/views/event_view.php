<?php
$pageTitle = '07 - Calendario de Eventos';
$activeExercise = 7;
$depth = 1;
require __DIR__ . '/../../shared/header.php';

$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];

$diasSemana = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
$todayDate = date('Y-m-d');
?>

<div class="container py-2">
    <!-- Header Card -->
    <div class="card app-card mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="mvc-badge mb-2 d-inline-block">
                        <i class="bi bi-diagram-3-fill me-1"></i> Arquitectura MVC (Model: Event, View: event_view, Controller: EventController)
                    </span>
                    <h2 class="h3 fw-bold mb-1">
                        <i class="bi bi-calendar-event text-primary me-2"></i>Calendario Interactivo de Eventos
                    </h2>
                    <p class="text-muted mb-0 small">
                        Planifica fechas clave, administra actividades y recibe alertas y notificaciones con recordatorios.
                    </p>
                </div>
                <div>
                    <button class="btn btn-brand px-3 py-2 d-flex align-items-center gap-2" id="btnNewEvent">
                        <i class="bi bi-calendar-plus"></i>
                        <span>Nuevo Evento</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Reminders Notification Banner -->
    <?php if (!empty($reminders)): ?>
        <div class="alert alert-warning border-0 shadow-sm rounded-4 p-3 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-bell-fill text-warning fs-5"></i>
                    <span>Recordatorios Próximos (Próximos 7 días)</span>
                </span>
                <span class="badge bg-warning text-dark"><?= count($reminders) ?> alertas</span>
            </div>
            <div class="row g-2">
                <?php foreach ($reminders as $rem): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="p-2 bg-white rounded-3 border small">
                            <div class="fw-bold text-dark d-flex justify-content-between">
                                <span><?= htmlspecialchars($rem['title']) ?></span>
                                <span class="badge bg-light text-muted border"><?= date('d/m', strtotime($rem['event_date'])) ?></span>
                            </div>
                            <div class="text-muted mt-1">
                                <i class="bi bi-clock me-1"></i><?= $rem['start_time'] ?> hrs
                                <?php if (!empty($rem['reminder_note'])): ?>
                                    &bull; <?= htmlspecialchars($rem['reminder_note']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Calendar Controls -->
    <div class="card app-card border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <a href="index.php?year=<?= $prevYear ?>&month=<?= $prevMonth ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-chevron-left"></i> Anterior
                </a>
                <a href="index.php?year=<?= date('Y') ?>&month=<?= date('m') ?>" class="btn btn-sm btn-outline-primary">
                    Hoy
                </a>
                <a href="index.php?year=<?= $nextYear ?>&month=<?= $nextMonth ?>" class="btn btn-outline-secondary btn-sm">
                    Siguiente <i class="bi bi-chevron-right"></i>
                </a>
            </div>
            <h4 class="mb-0 fw-bold text-dark">
                <?= $meses[$month] ?> de <?= $year ?>
            </h4>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary border px-3 py-2">
                    <?= array_sum(array_map('count', $eventsByDay)) ?> eventos este mes
                </span>
            </div>
        </div>

        <!-- Monthly Grid Table -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 calendar-table" style="min-width: 700px; table-layout: fixed;">
                    <thead>
                        <tr class="bg-light text-center small text-uppercase text-muted">
                            <?php foreach ($diasSemana as $dia): ?>
                                <th class="py-2" style="width: 14.28%;"><?= $dia ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $currentDay = 1;
                            $dayOfWeekCounter = 1;
                            echo '<tr>';

                            // Empty cells before the first day of the month
                            for ($i = 1; $i < $firstDayOfWeek; $i++) {
                                echo '<td class="bg-light opacity-50 p-2" style="height: 110px;"></td>';
                                $dayOfWeekCounter++;
                            }

                            // Days of current month
                            while ($currentDay <= $daysInMonth) {
                                if ($dayOfWeekCounter > 7) {
                                    echo '</tr><tr>';
                                    $dayOfWeekCounter = 1;
                                }

                                $fullDateStr = sprintf('%04d-%02d-%02d', $year, $month, $currentDay);
                                $isToday = ($fullDateStr === $todayDate);
                                $dayEvents = $eventsByDay[$currentDay] ?? [];

                                echo '<td class="p-2 position-relative align-top ' . ($isToday ? 'bg-primary-subtle bg-opacity-10 border-primary' : '') . '" style="height: 110px;">';
                                
                                // Day Header
                                echo '<div class="d-flex justify-content-between align-items-center mb-1">';
                                echo '<span class="badge ' . ($isToday ? 'bg-primary text-white' : 'text-dark fw-bold') . ' rounded-circle" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem;">' . $currentDay . '</span>';
                                echo '<button type="button" class="btn btn-link btn-sm text-decoration-none text-muted p-0 add-to-day-btn" data-date="' . $fullDateStr . '" title="Agregar evento en este día"><i class="bi bi-plus-circle"></i></button>';
                                echo '</div>';

                                // Events inside this day
                                echo '<div class="day-events-wrapper d-flex flex-column gap-1" style="max-height: 75px; overflow-y: auto;">';
                                foreach ($dayEvents as $ev) {
                                    $evColor = htmlspecialchars($ev['color']);
                                    echo '<div class="event-pill p-1 px-2 rounded small text-white text-truncate cursor-pointer" 
                                               style="background-color: ' . $evColor . '; font-size: 0.72rem; cursor: pointer;"
                                               data-id="' . $ev['id'] . '"
                                               data-title="' . htmlspecialchars($ev['title']) . '"
                                               data-desc="' . htmlspecialchars($ev['description'] ?? '') . '"
                                               data-date="' . $ev['event_date'] . '"
                                               data-start="' . $ev['start_time'] . '"
                                               data-end="' . $ev['end_time'] . '"
                                               data-cat="' . htmlspecialchars($ev['category']) . '"
                                               data-color="' . $evColor . '"
                                               data-reminder="' . $ev['has_reminder'] . '"
                                               data-remindernote="' . htmlspecialchars($ev['reminder_note'] ?? '') . '"
                                               title="' . htmlspecialchars($ev['title']) . ' (' . $ev['start_time'] . ')">';
                                    echo '<span class="fw-semibold">' . $ev['start_time'] . '</span> ' . htmlspecialchars($ev['title']);
                                    if ($ev['has_reminder']) {
                                        echo ' <i class="bi bi-bell-fill ms-1" style="font-size: 0.65rem;"></i>';
                                    }
                                    echo '</div>';
                                }
                                echo '</div>';

                                echo '</td>';

                                $currentDay++;
                                $dayOfWeekCounter++;
                            }

                            // Empty cells at the end of the month
                            while ($dayOfWeekCounter <= 7) {
                                echo '<td class="bg-light opacity-50 p-2" style="height: 110px;"></td>';
                                $dayOfWeekCounter++;
                            }

                            echo '</tr>';
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create / Edit Event -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="eventModalTitle">
                    <i class="bi bi-calendar-plus text-primary me-2"></i>Nuevo Evento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=save" method="POST" id="eventForm">
                <input type="hidden" name="id" id="eventId" value="0">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Título del Evento</label>
                        <input type="text" name="title" id="eventTitle" class="form-control" placeholder="Ej. Presentación de Resultados" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Descripción</label>
                        <textarea name="description" id="eventDesc" rows="2" class="form-control" placeholder="Detalles de la reunión..."></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Fecha</label>
                            <input type="date" name="event_date" id="eventDate" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hora Inicio</label>
                            <input type="time" name="start_time" id="eventStart" class="form-control" value="09:00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Hora Fin (Opc)</label>
                            <input type="time" name="end_time" id="eventEnd" class="form-control">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Categoría</label>
                            <select name="category" id="eventCat" class="form-select">
                                <option value="Trabajo">Trabajo</option>
                                <option value="Académico">Académico</option>
                                <option value="Personal">Personal</option>
                                <option value="Conferencia">Conferencia</option>
                                <option value="Urgente">Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Color del Evento</label>
                            <input type="color" name="color" id="eventColor" class="form-control form-control-color w-100" value="#4f46e5">
                        </div>
                    </div>
                    <!-- Reminder section -->
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" name="has_reminder" id="eventHasReminder" value="1">
                            <label class="form-check-label small fw-bold" for="eventHasReminder">Activar Recordatorio / Notificación</label>
                        </div>
                        <input type="text" name="reminder_note" id="eventReminderNote" class="form-control form-control-sm" placeholder="Nota de alerta (Ej. Preparar diapositivas)...">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light d-flex justify-content-between">
                    <div>
                        <a href="#" id="btnDeleteEvent" class="btn btn-outline-danger btn-sm" style="display: none;" onclick="return confirm('¿Seguro que deseas eliminar este evento?');">
                            <i class="bi bi-trash"></i> Eliminar
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-brand">Guardar Evento</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
const eventModalTitle = document.getElementById('eventModalTitle');
const eventId = document.getElementById('eventId');
const eventTitle = document.getElementById('eventTitle');
const eventDesc = document.getElementById('eventDesc');
const eventDate = document.getElementById('eventDate');
const eventStart = document.getElementById('eventStart');
const eventEnd = document.getElementById('eventEnd');
const eventCat = document.getElementById('eventCat');
const eventColor = document.getElementById('eventColor');
const eventHasReminder = document.getElementById('eventHasReminder');
const eventReminderNote = document.getElementById('eventReminderNote');
const btnDeleteEvent = document.getElementById('btnDeleteEvent');

function resetModal(defaultDate = '<?= date('Y-m-d') ?>') {
    eventModalTitle.innerHTML = '<i class="bi bi-calendar-plus text-primary me-2"></i>Nuevo Evento';
    eventId.value = 0;
    eventTitle.value = '';
    eventDesc.value = '';
    eventDate.value = defaultDate;
    eventStart.value = '09:00';
    eventEnd.value = '';
    eventCat.value = 'Trabajo';
    eventColor.value = '#4f46e5';
    eventHasReminder.checked = false;
    eventReminderNote.value = '';
    btnDeleteEvent.style.display = 'none';
}

document.getElementById('btnNewEvent').addEventListener('click', () => {
    resetModal();
    eventModal.show();
});

document.querySelectorAll('.add-to-day-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        resetModal(this.dataset.date);
        eventModal.show();
    });
});

document.querySelectorAll('.event-pill').forEach(pill => {
    pill.addEventListener('click', function() {
        eventModalTitle.innerHTML = '<i class="bi bi-pencil-square text-primary me-2"></i>Editar Evento';
        const id = this.dataset.id;
        eventId.value = id;
        eventTitle.value = this.dataset.title;
        eventDesc.value = this.dataset.desc;
        eventDate.value = this.dataset.date;
        eventStart.value = this.dataset.start;
        eventEnd.value = this.dataset.end;
        eventCat.value = this.dataset.cat;
        eventColor.value = this.dataset.color;
        eventHasReminder.checked = (this.dataset.reminder === '1');
        eventReminderNote.value = this.dataset.remindernote;
        
        btnDeleteEvent.href = `index.php?action=delete&id=${id}&year=<?= $year ?>&month=<?= $month ?>`;
        btnDeleteEvent.style.display = 'inline-block';

        eventModal.show();
    });
});
</script>

<?php require __DIR__ . '/../../shared/footer.php'; ?>
