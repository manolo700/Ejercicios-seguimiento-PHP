<?php
$pageTitle = '07 - Event Calendar';
$activeExercise = 7;
$depth = 1;
require __DIR__ . '/../../shared/header.php';

$monthNames = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];

$daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
$todayDate = date('Y-m-d');
?>

<div class="container py-2">
    <!-- Header Card -->
    <div class="card app-card mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="mvc-badge mb-2 d-inline-block">
                        <i class="bi bi-diagram-3-fill me-1"></i> MVC Architecture (Model: Event, View: event_view, Controller: EventController)
                    </span>
                    <h2 class="h3 fw-bold mb-1">
                        <i class="bi bi-calendar-event text-primary me-2"></i>Interactive Event Calendar
                    </h2>
                    <p class="text-muted mb-0 small">
                        Schedule key dates, organize activities, and receive automatic reminder notifications.
                    </p>
                </div>
                <div>
                    <button class="btn btn-brand px-3 py-2 d-flex align-items-center gap-2" id="btnNewEvent">
                        <i class="bi bi-calendar-plus"></i>
                        <span>New Event</span>
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
                    <span>Upcoming Reminders (Next 7 Days)</span>
                </span>
                <span class="badge bg-warning text-dark"><?= count($reminders) ?> alerts</span>
            </div>
            <div class="row g-2">
                <?php foreach ($reminders as $reminder): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="p-2 bg-white rounded-3 border small">
                            <div class="fw-bold text-dark d-flex justify-content-between">
                                <span><?= htmlspecialchars($reminder['title']) ?></span>
                                <span class="badge bg-light text-muted border"><?= date('m/d', strtotime($reminder['event_date'])) ?></span>
                            </div>
                            <div class="text-muted mt-1">
                                <i class="bi bi-clock me-1"></i><?= htmlspecialchars($reminder['start_time']) ?> hrs
                                <?php if (!empty($reminder['reminder_note'])): ?>
                                    &bull; <?= htmlspecialchars($reminder['reminder_note']) ?>
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
                    <i class="bi bi-chevron-left"></i> Previous
                </a>
                <a href="index.php?year=<?= date('Y') ?>&month=<?= date('m') ?>" class="btn btn-sm btn-outline-primary">
                    Today
                </a>
                <a href="index.php?year=<?= $nextYear ?>&month=<?= $nextMonth ?>" class="btn btn-outline-secondary btn-sm">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
            </div>
            <h4 class="mb-0 fw-bold text-dark">
                <?= $monthNames[$month] ?> <?= $year ?>
            </h4>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary border px-3 py-2">
                    <?= array_sum(array_map('count', $eventsByDay)) ?> events this month
                </span>
            </div>
        </div>

        <!-- Monthly Grid Table -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 calendar-table" style="min-width: 700px; table-layout: fixed;">
                    <thead>
                        <tr class="bg-light text-center small text-uppercase text-muted">
                            <?php foreach ($daysOfWeek as $dayName): ?>
                                <th class="py-2" style="width: 14.28%;"><?= $dayName ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $totalCells = ($firstDayOfWeek - 1) + $daysInMonth;
                            $totalRows = (int)ceil($totalCells / 7);
                            $totalSlots = $totalRows * 7;
                            $currentDay = 1;

                            for ($slot = 1; $slot <= $totalSlots; $slot++) {
                                if (($slot - 1) % 7 === 0) {
                                    echo '<tr>';
                                }

                                if ($slot < $firstDayOfWeek || $currentDay > $daysInMonth) {
                                    echo '<td class="bg-light opacity-50 p-2" style="height: 110px;"></td>';
                                } else {
                                    $dateString = sprintf('%04d-%02d-%02d', $year, $month, $currentDay);
                                    $isToday = ($dateString === $todayDate);
                                    $dayEvents = $eventsByDay[$currentDay] ?? [];

                                    echo '<td class="p-2 position-relative align-top ' . ($isToday ? 'bg-primary-subtle bg-opacity-10 border-primary' : '') . '" style="height: 110px;">';
                                    
                                    // Day Header
                                    echo '<div class="d-flex justify-content-between align-items-center mb-1">';
                                    echo '<span class="badge ' . ($isToday ? 'bg-primary text-white' : 'text-dark fw-bold') . ' rounded-circle" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem;">' . $currentDay . '</span>';
                                    echo '<button type="button" class="btn btn-link btn-sm text-decoration-none text-muted p-0 add-to-day-btn" data-date="' . $dateString . '" title="Add event on this date"><i class="bi bi-plus-circle"></i></button>';
                                    echo '</div>';

                                    // Events inside this day
                                    echo '<div class="day-events-wrapper d-flex flex-column gap-1" style="max-height: 75px; overflow-y: auto;">';
                                    foreach ($dayEvents as $event) {
                                        $eventColor = htmlspecialchars($event['color']);
                                        echo '<div class="event-pill p-1 px-2 rounded small text-white text-truncate cursor-pointer" 
                                                   style="background-color: ' . $eventColor . '; font-size: 0.72rem; cursor: pointer;"
                                                   data-id="' . $event['id'] . '"
                                                   data-title="' . htmlspecialchars($event['title']) . '"
                                                   data-desc="' . htmlspecialchars($event['description'] ?? '') . '"
                                                   data-date="' . $event['event_date'] . '"
                                                   data-start="' . $event['start_time'] . '"
                                                   data-end="' . $event['end_time'] . '"
                                                   data-cat="' . htmlspecialchars($event['category']) . '"
                                                   data-color="' . $eventColor . '"
                                                   data-reminder="' . $event['has_reminder'] . '"
                                                   data-remindernote="' . htmlspecialchars($event['reminder_note'] ?? '') . '"
                                                   title="' . htmlspecialchars($event['title']) . ' (' . $event['start_time'] . ')">';
                                        echo '<span class="fw-semibold">' . $event['start_time'] . '</span> ' . htmlspecialchars($event['title']);
                                        if ($event['has_reminder']) {
                                            echo ' <i class="bi bi-bell-fill ms-1" style="font-size: 0.65rem;"></i>';
                                        }
                                        echo '</div>';
                                    }
                                    echo '</div>';
                                    echo '</td>';

                                    $currentDay++;
                                }

                                if ($slot % 7 === 0) {
                                    echo '</tr>';
                                }
                            }
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
                    <i class="bi bi-calendar-plus text-primary me-2"></i>New Event
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=save" method="POST" id="eventForm">
                <input type="hidden" name="id" id="eventId" value="0">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Event Title</label>
                        <input type="text" name="title" id="eventTitle" class="form-control" placeholder="e.g., Team Project Review" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea name="description" id="eventDesc" rows="2" class="form-control" placeholder="Meeting details, agenda..."></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Date</label>
                            <input type="date" name="event_date" id="eventDate" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Start Time</label>
                            <input type="time" name="start_time" id="eventStart" class="form-control" value="09:00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">End Time (Opt)</label>
                            <input type="time" name="end_time" id="eventEnd" class="form-control">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Category</label>
                            <select name="category" id="eventCat" class="form-select">
                                <option value="Work">Work</option>
                                <option value="Academic">Academic</option>
                                <option value="Personal">Personal</option>
                                <option value="Conference">Conference</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Event Color</label>
                            <input type="color" name="color" id="eventColor" class="form-control form-control-color w-100" value="#4f46e5">
                        </div>
                    </div>
                    <!-- Reminder section -->
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" name="has_reminder" id="eventHasReminder" value="1">
                            <label class="form-check-label small fw-bold" for="eventHasReminder">Enable Reminder / Notification</label>
                        </div>
                        <input type="text" name="reminder_note" id="eventReminderNote" class="form-control form-control-sm" placeholder="Alert note (e.g., Prepare slide deck)...">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light d-flex justify-content-between">
                    <div>
                        <a href="#" id="btnDeleteEvent" class="btn btn-outline-danger btn-sm" style="display: none;" onclick="return confirm('Are you sure you want to delete this event?');">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-brand">Save Event</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('eventModal');
    if (!modalElement) return;

    const eventModal = new bootstrap.Modal(modalElement);
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
        eventModalTitle.innerHTML = '<i class="bi bi-calendar-plus text-primary me-2"></i>New Event';
        eventId.value = 0;
        eventTitle.value = '';
        eventDesc.value = '';
        eventDate.value = defaultDate;
        eventStart.value = '09:00';
        eventEnd.value = '';
        eventCat.value = 'Work';
        eventColor.value = '#4f46e5';
        eventHasReminder.checked = false;
        eventReminderNote.value = '';
        btnDeleteEvent.style.display = 'none';
    }

    const btnNew = document.getElementById('btnNewEvent');
    if (btnNew) {
        btnNew.addEventListener('click', function() {
            resetModal();
            eventModal.show();
        });
    }

    document.querySelectorAll('.add-to-day-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            resetModal(this.dataset.date);
            eventModal.show();
        });
    });

    document.querySelectorAll('.event-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            eventModalTitle.innerHTML = '<i class="bi bi-pencil-square text-primary me-2"></i>Edit Event';
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
});
</script>

<?php require __DIR__ . '/../../shared/footer.php'; ?>
