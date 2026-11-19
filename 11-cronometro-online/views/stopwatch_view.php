<?php
$pageTitle = '11 - High-Precision Online Stopwatch';
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
                        <i class="bi bi-diagram-3-fill me-1"></i> MVC Architecture (Model: Stopwatch, View: stopwatch_view, Controller: StopwatchController)
                    </span>
                    <h2 class="h3 fw-bold mb-1">
                        <i class="bi bi-stopwatch text-primary me-2"></i>High-Precision Online Stopwatch
                    </h2>
                    <p class="text-muted mb-0 small">
                        Start, pause, reset, record laps / split times, and persist benchmark sessions to SQLite.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Stopwatch Console Card -->
    <div class="card app-card border-0 mb-4 shadow" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="card-body p-5 text-center text-white">
            <span class="badge bg-secondary bg-opacity-50 text-light px-3 py-1 mb-2 text-uppercase" style="letter-spacing: 1px;" id="timerStatus">Stopped</span>
            
            <!-- Large Digital Display -->
            <div class="timer-display my-3 text-info" id="displayTime">
                00:00:00.00
            </div>

            <!-- Lap Current Split Small -->
            <div class="text-white-50 small mb-4 font-monospace fs-6">
                Current lap: <span id="currentLapDisplay">00:00.00</span>
            </div>

            <!-- Controls Buttons -->
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <button type="button" class="btn btn-success btn-lg px-4 py-2 fw-bold" id="btnStart">
                    <i class="bi bi-play-fill me-1"></i> Start
                </button>
                <button type="button" class="btn btn-warning btn-lg px-4 py-2 fw-bold" id="btnPause" style="display: none;">
                    <i class="bi bi-pause-fill me-1"></i> Pause
                </button>
                <button type="button" class="btn btn-primary btn-lg px-4 py-2 fw-bold" id="btnLap" disabled>
                    <i class="bi bi-flag-fill me-1"></i> Lap / Split
                </button>
                <button type="button" class="btn btn-danger btn-lg px-4 py-2 fw-bold" id="btnReset" disabled>
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </button>
                <button type="button" class="btn btn-outline-light btn-lg px-4 py-2 fw-bold" id="btnSaveModal" data-bs-toggle="modal" data-bs-target="#saveSessionModal" disabled>
                    <i class="bi bi-save me-1"></i> Save Session
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
                        <i class="bi bi-flag text-primary me-2"></i>Lap Records
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary rounded-pill" id="lapsBadge">0 laps</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnExportCSV" disabled>
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="lapsEmptyMessage" class="text-center py-5 text-muted">
                        <i class="bi bi-stopwatch-fill display-4 opacity-50"></i>
                        <h6 class="mt-3">No laps recorded yet</h6>
                        <p class="small text-muted">Click "Lap / Split" while the stopwatch is running to record split times.</p>
                    </div>

                    <div class="table-responsive" id="lapsTableWrapper" style="display: none; max-height: 380px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 font-monospace small">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Lap #</th>
                                    <th>Lap Time</th>
                                    <th>Total Elapsed Time</th>
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
                        <i class="bi bi-journal-bookmark text-primary me-2"></i>Session History
                    </h5>
                    <?php if (!empty($sessions)): ?>
                        <a href="index.php?action=clear_history" class="btn btn-sm btn-outline-danger" onclick="return confirm('Clear entire history?');">
                            <i class="bi bi-trash"></i> Clear
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($sessions)): ?>
                        <div class="text-center py-5 text-muted small">
                            No saved sessions in the database yet.
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($sessions as $s): ?>
                                <div class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-dark"><?= htmlspecialchars($s['session_name']) ?></span>
                                        <span class="badge bg-light text-dark border small"><?= date('M d, H:i', strtotime($s['created_at'])) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between text-muted small mt-2">
                                        <span>Total: <strong class="text-primary font-monospace"><?= Stopwatch::formatMs($s['total_time_ms']) ?></strong></span>
                                        <span><?= $s['total_laps'] ?> laps</span>
                                    </div>
                                    <?php if ($s['fastest_lap_ms']): ?>
                                        <div class="small text-success mt-1">
                                            <i class="bi bi-lightning-charge me-1"></i>Fastest: <?= Stopwatch::formatMs($s['fastest_lap_ms']) ?>
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
                    <i class="bi bi-save text-primary me-2"></i>Save Stopwatch Session
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=save_session" method="POST">
                <input type="hidden" name="total_time_ms" id="inputTotalTimeMs" value="0">
                <input type="hidden" name="laps_json" id="inputLapsJson" value="[]">

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Session Name / Label</label>
                        <input type="text" name="session_name" class="form-control" placeholder="e.g. 100m Sprint, Training Run..." required autofocus>
                    </div>
                    <div class="p-3 bg-light rounded-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Total Elapsed Time:</span>
                            <strong id="modalSummaryTotal" class="font-monospace">00:00.00</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Recorded Laps:</span>
                            <strong id="modalSummaryLaps">0</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand">Save Session</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
        if (displayTime) displayTime.textContent = formatMs(currentTotal);

        const currentLapTotal = currentTotal - lastLapTime;
        if (currentLapDisplay) currentLapDisplay.textContent = formatMs(currentLapTotal);
    }

    if (btnStart) {
        btnStart.addEventListener('click', function() {
            if (!isRunning) {
                isRunning = true;
                startTime = Date.now();
                timerInterval = setInterval(updateDisplays, 25);

                btnStart.style.display = 'none';
                if (btnPause) btnPause.style.display = 'inline-block';
                if (btnLap) btnLap.disabled = false;
                if (btnReset) btnReset.disabled = false;
                if (btnSaveModal) btnSaveModal.disabled = true;

                if (timerStatus) {
                    timerStatus.textContent = 'Running';
                    timerStatus.className = 'badge bg-success text-light px-3 py-1 mb-2 text-uppercase';
                }
            }
        });
    }

    if (btnPause) {
        btnPause.addEventListener('click', function() {
            if (isRunning) {
                isRunning = false;
                clearInterval(timerInterval);
                elapsedTime += Date.now() - startTime;
                updateDisplays();

                if (btnStart) {
                    btnStart.style.display = 'inline-block';
                    btnStart.innerHTML = '<i class="bi bi-play-fill me-1"></i> Resume';
                }
                btnPause.style.display = 'none';
                if (btnLap) btnLap.disabled = true;
                if (btnSaveModal) btnSaveModal.disabled = false;

                if (timerStatus) {
                    timerStatus.textContent = 'Paused';
                    timerStatus.className = 'badge bg-warning text-dark px-3 py-1 mb-2 text-uppercase';
                }
            }
        });
    }

    if (btnReset) {
        btnReset.addEventListener('click', function() {
            isRunning = false;
            clearInterval(timerInterval);
            startTime = 0;
            elapsedTime = 0;
            lastLapTime = 0;
            laps = [];

            if (displayTime) displayTime.textContent = '00:00:00.00';
            if (currentLapDisplay) currentLapDisplay.textContent = '00:00.00';

            if (btnStart) {
                btnStart.style.display = 'inline-block';
                btnStart.innerHTML = '<i class="bi bi-play-fill me-1"></i> Start';
            }
            if (btnPause) btnPause.style.display = 'none';
            if (btnLap) btnLap.disabled = true;
            btnReset.disabled = true;
            if (btnSaveModal) btnSaveModal.disabled = true;
            if (btnExportCSV) btnExportCSV.disabled = true;

            if (timerStatus) {
                timerStatus.textContent = 'Stopped';
                timerStatus.className = 'badge bg-secondary bg-opacity-50 text-light px-3 py-1 mb-2 text-uppercase';
            }

            renderLaps();
        });
    }

    if (btnLap) {
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
    }

    function renderLaps() {
        if (!lapsEmptyMessage || !lapsTableWrapper || !lapsBadge) return;

        if (laps.length === 0) {
            lapsEmptyMessage.style.display = 'block';
            lapsTableWrapper.style.display = 'none';
            lapsBadge.textContent = '0 laps';
            if (btnExportCSV) btnExportCSV.disabled = true;
            return;
        }

        lapsEmptyMessage.style.display = 'none';
        lapsTableWrapper.style.display = 'block';
        lapsBadge.textContent = laps.length + ' lap' + (laps.length > 1 ? 's' : '');
        if (btnExportCSV) btnExportCSV.disabled = false;

        let minMs = Infinity;
        let maxMs = -Infinity;
        if (laps.length >= 2) {
            laps.forEach(l => {
                if (l.lap_ms < minMs) minMs = l.lap_ms;
                if (l.lap_ms > maxMs) maxMs = l.lap_ms;
            });
        }

        if (lapsTableBody) {
            lapsTableBody.innerHTML = '';
            laps.forEach(l => {
                const tr = document.createElement('tr');
                let lapClass = '';
                let badge = '';

                if (laps.length >= 2) {
                    if (l.lap_ms === minMs) {
                        lapClass = 'lap-fastest';
                        badge = ' <span class="badge bg-success-subtle text-success ms-1">Fastest</span>';
                    } else if (l.lap_ms === maxMs) {
                        lapClass = 'lap-slowest';
                        badge = ' <span class="badge bg-danger-subtle text-danger ms-1">Slowest</span>';
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
    }

    if (btnSaveModal) {
        btnSaveModal.addEventListener('click', function() {
            const inputTotal = document.getElementById('inputTotalTimeMs');
            const inputLaps = document.getElementById('inputLapsJson');
            const modalTotal = document.getElementById('modalSummaryTotal');
            const modalLaps = document.getElementById('modalSummaryLaps');

            if (inputTotal) inputTotal.value = elapsedTime;
            if (inputLaps) inputLaps.value = JSON.stringify(laps);
            if (modalTotal) modalTotal.textContent = formatMs(elapsedTime);
            if (modalLaps) modalLaps.textContent = laps.length;
        });
    }

    if (btnExportCSV) {
        btnExportCSV.addEventListener('click', function() {
            if (laps.length === 0) return;
            let csv = 'Lap,Lap Time,Overall Elapsed Time\n';
            laps.forEach(l => {
                csv += `${l.number},"${formatMs(l.lap_ms)}","${formatMs(l.overall_ms)}"\n`;
            });
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `stopwatch_laps_${Date.now()}.csv`;
            link.click();
        });
    }
});
</script>

<?php require __DIR__ . '/../../shared/footer.php'; ?>
