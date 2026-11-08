<?php
$pageTitle = '10 - Plataforma de Encuestas';
$activeExercise = 10;
$depth = 1;
require __DIR__ . '/../../shared/header.php';
?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container py-2">
    <!-- Header Card -->
    <div class="card app-card mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="mvc-badge mb-2 d-inline-block">
                        <i class="bi bi-diagram-3-fill me-1"></i> Arquitectura MVC (Model: Poll, View: poll_view, Controller: PollController)
                    </span>
                    <h2 class="h3 fw-bold mb-1">
                        <i class="bi bi-bar-chart-line text-primary me-2"></i>Plataforma de Encuestas & Análisis de Opinión
                    </h2>
                    <p class="text-muted mb-0 small">
                        Crea votaciones públicas, recopila respuestas en tiempo real y visualiza gráficos estadísticos dinámicos.
                    </p>
                </div>
                <div>
                    <button class="btn btn-brand px-3 py-2 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#newPollModal">
                        <i class="bi bi-plus-circle"></i>
                        <span>Crear Encuesta</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Available Polls List Column -->
        <div class="col-lg-4">
            <div class="card app-card border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-collection text-primary me-2"></i>Encuestas Activas
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($polls)): ?>
                        <div class="p-4 text-center text-muted small">No hay encuestas creadas.</div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($polls as $p): ?>
                                <?php $isSelected = ($activePoll && $activePoll['id'] === $p['id']); ?>
                                <a href="index.php?poll_id=<?= $p['id'] ?>" class="list-group-item list-group-item-action p-3 <?= $isSelected ? 'bg-primary-subtle text-primary border-primary' : '' ?>">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-light text-dark border small"><?= htmlspecialchars($p['category']) ?></span>
                                        <span class="badge bg-secondary-subtle text-secondary small"><?= $p['total_votes'] ?> votos</span>
                                    </div>
                                    <div class="fw-bold text-dark <?= $isSelected ? 'text-primary' : '' ?>">
                                        <?= htmlspecialchars($p['title']) ?>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <i class="bi bi-clock me-1"></i><?= date('d/m/Y', strtotime($p['created_at'])) ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Selected Poll & Results Column -->
        <div class="col-lg-8">
            <?php if ($activePoll): ?>
                <div class="card app-card border-0 mb-4">
                    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-primary me-2"><?= htmlspecialchars($activePoll['category']) ?></span>
                            <span class="small text-muted"><i class="bi bi-people-fill me-1"></i><?= $activePoll['total_votes'] ?> votos totales</span>
                        </div>
                        <a href="index.php?action=delete&id=<?= $activePoll['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta encuesta?');">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>

                    <div class="card-body p-4">
                        <h4 class="fw-bold mb-2 text-dark"><?= htmlspecialchars($activePoll['title']) ?></h4>
                        <?php if (!empty($activePoll['description'])): ?>
                            <p class="text-muted mb-4"><?= htmlspecialchars($activePoll['description']) ?></p>
                        <?php endif; ?>

                        <!-- Voting Form if user hasn't voted yet -->
                        <?php if (!$hasVoted): ?>
                            <form action="index.php?action=vote" method="POST" class="mb-4 p-3 bg-light rounded-4 border">
                                <input type="hidden" name="poll_id" value="<?= $activePoll['id'] ?>">
                                <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-check-circle me-1"></i>Selecciona tu respuesta:</h6>
                                
                                <div class="d-flex flex-column gap-2 mb-3">
                                    <?php foreach ($activePoll['options'] as $idx => $opt): ?>
                                        <div class="form-check p-3 bg-white rounded-3 border">
                                            <input class="form-check-input ms-0 me-2" type="radio" name="option_id" id="opt_<?= $opt['id'] ?>" value="<?= $opt['id'] ?>" required>
                                            <label class="form-check-label fw-medium w-100" for="opt_<?= $opt['id'] ?>">
                                                <?= htmlspecialchars($opt['option_text']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <button type="submit" class="btn btn-brand px-4 py-2">
                                    <i class="bi bi-send-fill me-1"></i> Enviar mi Voto
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-info border-0 rounded-4 d-flex align-items-center gap-2 mb-4">
                                <i class="bi bi-info-circle-fill fs-5"></i>
                                <span>Ya has registrado tu voto en esta encuesta. A continuación se presentan las estadísticas consolidadas.</span>
                            </div>
                        <?php endif; ?>

                        <!-- Visual Breakdown / Progress Bars -->
                        <h5 class="fw-bold mb-3"><i class="bi bi-graph-up me-2 text-primary"></i>Resultados Detallados</h5>
                        <div class="d-flex flex-column gap-3 mb-4">
                            <?php foreach ($activePoll['options'] as $opt): ?>
                                <div>
                                    <div class="d-flex justify-content-between align-items-center fw-bold small mb-1">
                                        <span><?= htmlspecialchars($opt['option_text']) ?></span>
                                        <span><?= $opt['votes'] ?> votos (<?= $opt['percent'] ?>%)</span>
                                    </div>
                                    <div class="progress" style="height: 12px; border-radius: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $opt['percent'] ?>%;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Visual Chart Canvas -->
                        <div class="p-3 bg-light rounded-4 border">
                            <h6 class="fw-bold text-center mb-3">Gráfico Estadístico Visual</h6>
                            <div style="max-height: 280px; position: relative;">
                                <canvas id="pollChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card app-card border-0 text-center py-5">
                    <div class="card-body">
                        <i class="bi bi-bar-chart display-3 text-muted"></i>
                        <h5 class="mt-3">Selecciona o crea una encuesta</h5>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Create Poll -->
<div class="modal fade" id="newPollModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle text-primary me-2"></i>Nueva Encuesta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=create" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Pregunta o Título de la Encuesta</label>
                        <input type="text" name="title" class="form-control" placeholder="¿Cuál es tu opinión sobre...?" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Descripción (Opcional)</label>
                        <textarea name="description" rows="2" class="form-control" placeholder="Contexto adicional..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Categoría</label>
                        <select name="category" class="form-select">
                            <option value="Tecnología">Tecnología</option>
                            <option value="Educación">Educación</option>
                            <option value="Trabajo">Trabajo</option>
                            <option value="Estilo de Vida">Estilo de Vida</option>
                            <option value="General">General</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small fw-bold mb-0">Opciones de Respuesta</label>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddOption">
                                <i class="bi bi-plus"></i> Añadir opción
                            </button>
                        </div>
                        <div id="optionsContainer" class="d-flex flex-column gap-2">
                            <input type="text" name="options[]" class="form-control form-control-sm" placeholder="Opción 1" required>
                            <input type="text" name="options[]" class="form-control form-control-sm" placeholder="Opción 2" required>
                            <input type="text" name="options[]" class="form-control form-control-sm" placeholder="Opción 3">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-brand">Publicar Encuesta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('btnAddOption').addEventListener('click', function() {
    const container = document.getElementById('optionsContainer');
    const count = container.children.length + 1;
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'options[]';
    input.className = 'form-control form-control-sm';
    input.placeholder = 'Opción ' + count;
    container.appendChild(input);
});

<?php if ($activePoll): ?>
const ctx = document.getElementById('pollChart').getContext('2d');
const labels = <?= json_encode(array_column($activePoll['options'], 'option_text')) ?>;
const data = <?= json_encode(array_column($activePoll['options'], 'votes')) ?>;

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Votos',
            data: data,
            backgroundColor: [
                '#4f46e5', '#3b82f6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'
            ],
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});
<?php endif; ?>
</script>

<?php require __DIR__ . '/../../shared/footer.php'; ?>
