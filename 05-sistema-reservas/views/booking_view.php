<?php
$pageTitle = '05 - Sistema de Reservas';
$activeExercise = 5;
$depth = 1;
require __DIR__ . '/../../shared/header.php';
?>

<div class="container py-2">
    <!-- Header Card -->
    <div class="card app-card mb-4 border-0">
        <div class="card-body p-4">
            <span class="mvc-badge mb-2 d-inline-block">
                <i class="bi bi-diagram-3-fill me-1"></i> Arquitectura MVC (Model: Booking, View: booking_view, Controller: BookingController)
            </span>
            <h2 class="h3 fw-bold mb-1">
                <i class="bi bi-calendar-check text-primary me-2"></i>Sistema de Reservas y Citas en Línea
            </h2>
            <p class="text-muted mb-0 small">
                Consulta disponibilidad de horarios en tiempo real, reserva servicios profesionales y obtén confirmación inmediata con código de ticket.
            </p>
        </div>
    </div>

    <!-- Voucher banner if confirmed -->
    <?php if ($confirmedBooking): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4">
            <div class="d-flex align-items-start gap-3">
                <div class="p-3 bg-success text-white rounded-circle">
                    <i class="bi bi-check2-circle fs-2"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="alert-heading fw-bold mb-1">¡Reserva Confirmada Exitosamente!</h4>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                            Código: <?= htmlspecialchars($confirmedBooking['booking_code']) ?>
                        </span>
                    </div>
                    <p class="mb-2">Hemos agendado tu cita con los siguientes datos:</p>
                    <div class="row g-2 small">
                        <div class="col-md-3"><strong>Cliente:</strong> <?= htmlspecialchars($confirmedBooking['client_name']) ?></div>
                        <div class="col-md-3"><strong>Servicio:</strong> <?= htmlspecialchars(Booking::SERVICES[$confirmedBooking['service_id']]['name'] ?? $confirmedBooking['service_id']) ?></div>
                        <div class="col-md-3"><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($confirmedBooking['booking_date'])) ?></div>
                        <div class="col-md-3"><strong>Hora:</strong> <?= htmlspecialchars($confirmedBooking['booking_time']) ?> hrs</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Booking Process Column -->
        <div class="col-lg-7">
            <div class="card app-card border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-clock-history text-primary me-2"></i>Paso 1: Selecciona Fecha y Servicio
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="index.php" method="GET" class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Fecha deseada</label>
                            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($selectedDate) ?>" min="<?= date('Y-m-d') ?>" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Servicio Profesional</label>
                            <select name="service" class="form-select" onchange="this.form.submit()">
                                <?php foreach ($services as $id => $s): ?>
                                    <option value="<?= $id ?>" <?= $selectedService === $id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['name']) ?> ($<?= number_format($s['price'], 2) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>

                    <!-- Slot Selector -->
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-grid-fill text-primary"></i>
                        <span>Horarios Disponibles para <?= date('d/m/Y', strtotime($selectedDate)) ?>:</span>
                    </h6>
                    <div class="row g-2 mb-4">
                        <?php foreach ($slots as $slot): ?>
                            <div class="col-6 col-sm-4 col-md-3">
                                <?php if ($slot['available']): ?>
                                    <button type="button" class="btn btn-outline-success w-100 py-2 slot-btn fw-bold" data-time="<?= $slot['time'] ?>">
                                        <i class="bi bi-check-circle me-1"></i><?= $slot['time'] ?>
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-light text-muted border w-100 py-2" disabled>
                                        <i class="bi bi-x-circle me-1"></i><?= $slot['time'] ?>
                                        <small class="d-block" style="font-size: 0.65rem;">Ocupado</small>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Step 2 Form (Triggered upon choosing slot) -->
                    <div class="p-3 bg-light rounded-3 border" id="clientDetailsSection" style="display: none;">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-person-fill-check me-2"></i>Paso 2: Confirmar Reserva para las <span id="displaySelectedTime" class="badge bg-primary fs-6">--:--</span>
                        </h6>
                        <form action="index.php?action=book" method="POST">
                            <input type="hidden" name="booking_date" value="<?= htmlspecialchars($selectedDate) ?>">
                            <input type="hidden" name="service_id" value="<?= htmlspecialchars($selectedService) ?>">
                            <input type="hidden" name="booking_time" id="inputBookingTime" value="">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Nombre Completo</label>
                                    <input type="text" name="client_name" class="form-control" placeholder="Ej. Ana Morales" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Correo Electrónico</label>
                                    <input type="email" name="client_email" class="form-control" placeholder="ana@ejemplo.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Teléfono de Contacto</label>
                                    <input type="tel" name="client_phone" class="form-control" placeholder="+52 55 1234 5678" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Motivo o Notas (Opcional)</label>
                                    <input type="text" name="notes" class="form-control" placeholder="Primera vez...">
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-brand px-4 py-2">
                                        <i class="bi bi-check2-circle me-1"></i> Confirmar y Generar Cita
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings / Administration List -->
        <div class="col-lg-5">
            <div class="card app-card border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-card-checklist text-primary me-2"></i>Reservas Registradas
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentBookings)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x display-4"></i>
                            <h6 class="mt-3">No hay reservas aún</h6>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentBookings as $bk): ?>
                                <?php 
                                    $isCancelled = ($bk['status'] === 'CANCELADA');
                                    $serviceName = Booking::SERVICES[$bk['service_id']]['name'] ?? $bk['service_id'];
                                ?>
                                <div class="list-group-item p-3 <?= $isCancelled ? 'bg-light opacity-75' : '' ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-primary-subtle text-primary border font-monospace">
                                            <?= htmlspecialchars($bk['booking_code']) ?>
                                        </span>
                                        <span class="badge bg-<?= $isCancelled ? 'secondary' : 'success' ?>-subtle text-<?= $isCancelled ? 'secondary' : 'success' ?>">
                                            <?= htmlspecialchars($bk['status']) ?>
                                        </span>
                                    </div>
                                    <div class="fw-semibold text-dark"><?= htmlspecialchars($bk['client_name']) ?></div>
                                    <div class="small text-muted"><?= htmlspecialchars($serviceName) ?></div>
                                    <div class="small text-muted d-flex align-items-center gap-3 mt-1">
                                        <span><i class="bi bi-calendar me-1"></i><?= date('d/m/Y', strtotime($bk['booking_date'])) ?></span>
                                        <span><i class="bi bi-clock me-1"></i><?= htmlspecialchars($bk['booking_time']) ?> hrs</span>
                                    </div>

                                    <?php if (!$isCancelled): ?>
                                        <div class="text-end mt-2">
                                            <a href="index.php?action=cancel&code=<?= urlencode($bk['booking_code']) ?>" 
                                               class="btn btn-sm btn-link text-danger text-decoration-none p-0"
                                               onclick="return confirm('¿Deseas cancelar esta reserva?');">
                                                <i class="bi bi-x-circle me-1"></i> Cancelar Cita
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const slotButtons = document.querySelectorAll('.slot-btn');
    const clientSection = document.getElementById('clientDetailsSection');
    const displaySelectedTime = document.getElementById('displaySelectedTime');
    const inputBookingTime = document.getElementById('inputBookingTime');

    slotButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            slotButtons.forEach(b => {
                b.classList.remove('btn-success', 'text-white');
                b.classList.add('btn-outline-success');
            });
            this.classList.remove('btn-outline-success');
            this.classList.add('btn-success', 'text-white');

            const time = this.dataset.time;
            if (displaySelectedTime) displaySelectedTime.textContent = time + ' hrs';
            if (inputBookingTime) inputBookingTime.value = time;
            if (clientSection) {
                clientSection.style.display = 'block';
                clientSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    });
});
</script>

<?php require __DIR__ . '/../../shared/footer.php'; ?>

