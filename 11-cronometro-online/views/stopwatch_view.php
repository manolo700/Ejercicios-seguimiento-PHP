<?php
$pageTitle = '11 - Cronómetro Online';
$activeExercise = 11;
$depth = 1;
require __DIR__ . '/../../shared/header.php';
?>

<style>
.timer-display {
    font-family: 'Courier New', Courier, monospace;
    font-size: clamp(2.8rem, 8vw, 5.5rem);
    font-weight: 800;
    letter-spacing: 2px;
}
.lap-fastest {
    color: #16a34a !important;
    font-weight: 700;
}
.lap-slowest {
    color: #dc2626 !important;
    font-weight: 700;
}
</style>

<div class="container py-2">
    <!-- Header Card -->
    <div class="card app-card mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="mvc-badge mb-2 d-inline-block">
                        <i class="bi bi-diagram-3-fill me-1"></i> Arquitectura MVC (Model: Stopwatch, View: stopwatch_view, Controller: StopwatchController)
                    </span>
                    <h2 class="h3 fw-bold mb-1">
                        <i class="bi bi-stopwatch text-primary me-2"></i>Cronómetro Online de Alta Precisión
                    </h2>
                    <p class="text-muted mb-0 small">
                        Funciones de inicio, pausa, reinicio, registro de vueltas / tiempos parciales y guardado de sesiones.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Stopwatch Console Card -->
    <div class="card app-card border-0 mb-4 shadow" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="card-body p-5 text-center text-white">
            <span class="badge bg-secondary bg-opacity-50 text-light px-3 py-1 mb-2 text-uppercase" style="letter-spacing: 1px;" id="timerStatus">Detenido</span>
            
            <!-- Large Digital Display -->
            <div class="timer-display my-3 text-info" id="displayTime">
                00:00:00.00
            </div>

            <!-- Lap Current Split Small -->
            <div class="text-white-50 small mb-4 font-monospace fs-6">
                Vuelta actual: <span id="currentLapDisplay">00:00.00</span>
            </div>

            <!-- Controls Buttons -->
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <button type="button" class="btn btn-success btn-lg px-4 py-2 fw-bold" id="btnStart">
                    <i class="bi bi-play-fill me-1"></i> Iniciar
                </button>
                <button type="button" class="btn btn-warning btn-lg px-4 py-2 fw-bold" id="btnPause" style="display: none;">
                    <i class="bi bi-pause-fill me-1"></i> Pausar
                </button>
                <button type="button" class="btn btn-primary btn-lg px-4 py-2 fw-bold" id="btnLap" disabled>
                    <i class="bi bi-flag-fill me-1"></i> Vuelta / Parcial
                </button>
                <button type="button" class="btn btn-danger btn-lg px-4 py-2 fw-bold" id="btnReset" disabled>
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reiniciar
                </button>
                <button type="button" class="btn btn-outline-light btn-lg px-4 py-2 fw-bold" id="btnSaveModal" data-bs-toggle="modal" data-bs-target="#saveSessionModal" disabled>
                    <i class="bi bi-save me-1"></i> Guardar Sesión
                </button>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Laps Table Column -->
        <div class="col-lg-7">
            <div class="card app-card border-0 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-flag text-primary me-2"></i>Registro de Vueltas
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-pill" id="lapsBadge">0 vueltas</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnExportCSV" disabled>
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Exportar CSV
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="lapsEmptyMessage" class="text-center py-5 text-muted">
                        <i class="bi bi-stopwatch-fill display-4 opacity-50"></i>
                        <h6 class="mt-3">No hay vueltas registradas</h6>
                        <p class="small text-muted">Haz clic en "Vuelta / Parcial" mientras el cronómetro corre para marcar tiempos.</p>
                    </div>

                    <div class="table-responsive" id="lapsTableWrapper" style="display: none; max-height: 380px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 font-monospace small">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Vuelta #</th>
                                    <th>Tiempo de Vuelta</th>
                                    <th>Tiempo Total Acumulado</th>
                                </tr>
                            </thead>
                            <tbody id="lapsTableBody">
                                <!-- Dynamic rows inserted via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Past Sessions Column -->
        <div class="col-lg-5">
            <div class="card app-card border-0 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-journal-bookmark text-primary me-2"></i>Historial de Sesiones
                    </h5>
                    <?php if (!empty($sessions)): ?>
                        <a href="index.php?action=clear_history" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Limpiar todo el historial?');">
                            <i class="bi bi-trash"></i> Limpiar
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($sessions)): ?>
                        <div class="text-center py-5 text-muted small">
                            No hay sesiones guardadas en la base de datos aún.
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($sessions as $s): ?>
                                <div class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($s['session_name']) ?></span>
                                        <span class="badge bg-light text-dark border small"><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted small mt-2">
                                        <span>Total: <strong class="text-primary font-monospace"><?= Stopwatch::formatMs($s['total_time_ms']) ?></strong></span>
                                        <span><?= $s['total_laps'] ?> vueltas</span>
                                    </div>
                                    <?php if ($s['fastest_lap_ms']): ?>
                                        <div class="small text-success mt-1">
                                            <i class="bi bi-lightning-charge me-1"></i>Más rápida: <?= Stopwatch::formatMs($s['fastest_lap_ms']) ?>
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

<!-- Save Session Modal -->
<div class="modal fade" id="saveSessionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-save text-primary me-2"></i>Guardar Sesión de Cronometraje
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=save_session" method="POST">
                <input type="hidden" name="total_time_ms" id="inputTotalTimeMs" value="0">
                <input type="hidden" name="laps_json" id="inputLapsJson" value="[]">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre o Etiqueta de la Sesión</label>
                        <input type="text" name="session_name" class="form-control" placeholder="Ej. Carrera de 100m, Entrenamiento..." required autofocus>
                    </div>
                    <div class="p-3 bg-light rounded-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Tiempo Total:</span>
                            <strong id="modalSummaryTotal" class="font-monospace">00:00.00</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Vueltas Registradas:</span>
                            <strong id="modalSummaryLaps">0</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-brand">Guardar en Base de Datos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let startTime = 0;
let elapsedTime = 0;
let timerInterval = null;
let isRunning = false;

let lastLapTime = 0;
let laps = [];

const displayTime = document.getElementById('displayTime');
const currentLapDisplay = document.getElementById('currentLapDisplay');
const timerStatus = document.getElementById('timerStatus');

const btnStart = document.getElementById('btnStart');
const btnPause = document.getElementById('btnPause');
const btnLap = document.getElementById('btnLap');
const btnReset = document.getElementById('btnReset');
const btnSaveModal = document.getElementById('btnSaveModal');
const btnExportCSV = document.getElementById('btnExportCSV');

const lapsTableBody = document.getElementById('lapsTableBody');
const lapsEmptyMessage = document.getElementById('lapsEmptyMessage');
const lapsTableWrapper = document.getElementById('lapsTableWrapper');
const lapsBadge = document.getElementById('lapsBadge');

function formatMs(ms) {
    const hours = Math.floor(ms / 3600000);
    const minutes = Math.floor((ms % 3600000) / 60000);
    const seconds = Math.floor((ms % 60000) / 1000);
    const millis = Math.floor((ms % 1000) / 10);

    const h = String(hours).padStart(2, '0');
    const m = String(minutes).padStart(2, '0');
    const s = String(seconds).padStart(2, '0');
    const msStr = String(millis).padStart(2, '0');

    return `${h}:${m}:${s}.${msStr}`;
}

function updateDisplays() {
    const now = Date.now();
    const currentTotal = elapsedTime + (isRunning ? (now - startTime) : 0);
    displayTime.textContent = formatMs(currentTotal);

    const currentLapTotal = currentTotal - lastLapTime;
    currentLapDisplay.textContent = formatMs(currentLapTotal);
}

btnStart.addEventListener('click', function() {
    if (!isRunning) {
        isRunning = true;
        startTime = Date.now();
        timerInterval = setInterval(updateDisplays, 25);

        btnStart.style.display = 'none';
        btnPause.style.display = 'inline-block';
        btnLap.disabled = false;
        btnReset.disabled = false;
        btnSaveModal.disabled = true;

        timerStatus.textContent = 'En ejecución';
        timerStatus.className = 'badge bg-success text-light px-3 py-1 mb-2 text-uppercase';
    }
});

btnPause.addEventListener('click', function() {
    if (isRunning) {
        isRunning = false;
        clearInterval(timerInterval);
        elapsedTime += Date.now() - startTime;
        updateDisplays();

        btnStart.style.display = 'inline-block';
        btnStart.innerHTML = '<i class="bi bi-play-fill me-1"></i> Reanudar';
        btnPause.style.display = 'none';
        btnLap.disabled = true;
        btnSaveModal.disabled = false;

        timerStatus.textContent = 'Pausado';
        timerStatus.className = 'badge bg-warning text-dark px-3 py-1 mb-2 text-uppercase';
    }
});

btnReset.addEventListener('click', function() {
    isRunning = false;
    clearInterval(timerInterval);
    startTime = 0;
    elapsedTime = 0;
    lastLapTime = 0;
    laps = [];

    displayTime.textContent = '00:00:00.00';
    currentLapDisplay.textContent = '00:00.00';

    btnStart.style.display = 'inline-block';
    btnStart.innerHTML = '<i class="bi bi-play-fill me-1"></i> Iniciar';
    btnPause.style.display = 'none';
    btnLap.disabled = true;
    btnReset.disabled = true;
    btnSaveModal.disabled = true;
    btnExportCSV.disabled = true;

    timerStatus.textContent = 'Detenido';
    timerStatus.className = 'badge bg-secondary bg-opacity-50 text-light px-3 py-1 mb-2 text-uppercase';

    renderLaps();
});

btnLap.addEventListener('click', function() {
    const currentTotal = elapsedTime + (Date.now() - startTime);
    const lapDuration = currentTotal - lastLapTime;
    lastLapTime = currentTotal;

    laps.unshift({
        number: laps.length + 1,
        lap_ms: lapDuration,
        overall_ms: currentTotal
    });

    renderLaps();
});

function renderLaps() {
    if (laps.length === 0) {
        lapsEmptyMessage.style.display = 'block';
        lapsTableWrapper.style.display = 'none';
        lapsBadge.textContent = '0 vueltas';
        btnExportCSV.disabled = true;
        return;
    }

    lapsEmptyMessage.style.display = 'none';
    lapsTableWrapper.style.display = 'block';
    lapsBadge.textContent = laps.length + ' vuelta' + (laps.length > 1 ? 's' : '');
    btnExportCSV.disabled = false;

    // Determine fastest & slowest
    let minMs = Infinity;
    let maxMs = -Infinity;
    if (laps.length >= 2) {
        laps.forEach(l => {
            if (l.lap_ms < minMs) minMs = l.lap_ms;
            if (l.lap_ms > maxMs) maxMs = l.lap_ms;
        });
    }

    lapsTableBody.innerHTML = '';
    laps.forEach(l => {
        const tr = document.createElement('tr');
        let lapClass = '';
        let badge = '';

        if (laps.length >= 2) {
            if (l.lap_ms === minMs) {
                lapClass = 'lap-fastest';
                badge = ' <span class="badge bg-success-subtle text-success ms-1">Rápida</span>';
            } else if (l.lap_ms === maxMs) {
                lapClass = 'lap-slowest';
                badge = ' <span class="badge bg-danger-subtle text-danger ms-1">Lenta</span>';
            }
        }

        tr.innerHTML = `
            <td>#${l.number}</td>
            <td class="${lapClass}">${formatMs(l.lap_ms)} ${badge}</td>
            <td>${formatMs(l.overall_ms)}</td>
        `;
        lapsTableBody.appendChild(tr);
    });
}

// Modal prefill
btnSaveModal.addEventListener('click', function() {
    document.getElementById('inputTotalTimeMs').value = elapsedTime;
    document.getElementById('inputLapsJson').value = JSON.stringify(laps);
    document.getElementById('modalSummaryTotal').textContent = formatMs(elapsedTime);
    document.getElementById('modalSummaryLaps').textContent = laps.length;
});

// CSV Export
btnExportCSV.addEventListener('click', function() {
    if (laps.length === 0) return;
    let csv = 'Vuelta,Tiempo de Vuelta,Tiempo Acumulado\n';
    laps.forEach(l => {
        csv += `${l.number},"${formatMs(l.lap_ms)}","${formatMs(l.overall_ms)}"\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `cronometro_vueltas_${Date.now()}.csv`;
    link.click();
});
</script>

<?php require __DIR__ . '/../../shared/footer.php'; ?>
