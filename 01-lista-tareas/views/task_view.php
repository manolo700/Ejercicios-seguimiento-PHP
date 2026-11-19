<?php
$pageTitle = '01 - Lista de Tareas';
$activeExercise = 1;
$depth = 1;
require __DIR__ . '/../../shared/header.php';
?>

<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <!-- Header Card -->
            <div class="card app-card mb-4 border-0">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                        <div>
                            <span class="mvc-badge mb-2 d-inline-block">
                                <i class="bi bi-diagram-3-fill me-1"></i> Arquitectura MVC (Model: Task, View: task_view, Controller: TaskController)
                            </span>
                            <h2 class="h3 fw-bold mb-1">
                                <i class="bi bi-check2-square text-primary me-2"></i>Lista de Tareas
                            </h2>
                            <p class="text-muted mb-0 small">
                                Agrega nuevas tareas, márcalas como completadas o elimínalas con persistencia en SQLite.
                            </p>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                                Total: <?= $stats['total'] ?>
                            </span>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill">
                                Pendientes: <?= $stats['pending'] ?>
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                Hechas: <?= $stats['completed'] ?>
                            </span>
                        </div>
                    </div>

                    <!-- Add Task Form -->
                    <form action="index.php?action=add" method="POST" class="mt-4">
                        <input type="hidden" name="current_filter" value="<?= htmlspecialchars($filter) ?>">
                        <div class="row g-2">
                            <div class="col-md-7 col-12">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="bi bi-pencil-square text-muted"></i>
                                    </span>
                                    <input type="text" name="title" class="form-control border-start-0 ps-0" placeholder="¿Qué necesitas hacer hoy?" required autofocus>
                                </div>
                            </div>
                            <div class="col-md-3 col-7">
                                <select name="priority" class="form-select">
                                    <option value="baja">Prioridad Baja</option>
                                    <option value="media" selected>Prioridad Media</option>
                                    <option value="alta">Prioridad Alta</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-5 d-grid">
                                <button type="submit" class="btn btn-brand">
                                    <i class="bi bi-plus-lg me-1"></i> Añadir
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Task List Card -->
            <div class="card app-card border-0">
                <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <ul class="nav nav-pills gap-1">
                        <li class="nav-item">
                            <a class="nav-link py-1 px-3 <?= $filter === 'all' ? 'active' : '' ?>" href="index.php?filter=all">
                                Todas <span class="badge bg-secondary ms-1"><?= $stats['total'] ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 px-3 <?= $filter === 'pending' ? 'active' : '' ?>" href="index.php?filter=pending">
                                Pendientes <span class="badge bg-warning text-dark ms-1"><?= $stats['pending'] ?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1 px-3 <?= $filter === 'completed' ? 'active' : '' ?>" href="index.php?filter=completed">
                                Completadas <span class="badge bg-success ms-1"><?= $stats['completed'] ?></span>
                            </a>
                        </li>
                    </ul>

                    <?php if ($stats['completed'] > 0): ?>
                        <a href="index.php?action=clear_completed&filter=<?= urlencode($filter) ?>" 
                           class="btn btn-sm btn-outline-danger" 
                           onclick="return confirm('¿Seguro que deseas eliminar todas las tareas completadas?');">
                            <i class="bi bi-trash3 me-1"></i> Limpiar completadas
                        </a>
                    <?php endif; ?>
                </div>

                <div class="card-body p-0">
                    <?php if (empty($tasks)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-clipboard2-check text-muted display-4"></i>
                            <h5 class="mt-3 text-secondary">No hay tareas en esta sección</h5>
                            <p class="text-muted small">Agrega una tarea arriba para comenzar.</p>
                        </div>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($tasks as $t): ?>
                                <?php 
                                    $isDone = (bool)$t['completed'];
                                    $prioClass = match($t['priority']) {
                                        'alta' => 'danger',
                                        'media' => 'warning',
                                        'baja' => 'success',
                                        default => 'secondary'
                                    };
                                ?>
                                <li class="list-group-item d-flex align-items-center justify-content-between py-3 px-4 <?= $isDone ? 'bg-light' : '' ?>">
                                    <div class="d-flex align-items-center gap-3 flex-grow-1 me-3">
                                        <a href="index.php?action=toggle&id=<?= $t['id'] ?>&filter=<?= urlencode($filter) ?>" class="text-decoration-none fs-4">
                                            <?php if ($isDone): ?>
                                                <i class="bi bi-check-circle-fill text-success"></i>
                                            <?php else: ?>
                                                <i class="bi bi-circle text-muted"></i>
                                            <?php endif; ?>
                                        </a>
                                        <div>
                                            <span class="fs-6 <?= $isDone ? 'text-decoration-line-through text-muted' : 'fw-medium text-dark' ?>">
                                                <?= htmlspecialchars($t['title']) ?>
                                            </span>
                                            <div class="small text-muted d-flex align-items-center gap-2 mt-1">
                                                <span class="badge bg-<?= $prioClass ?>-subtle text-<?= $prioClass ?> border border-<?= $prioClass ?>-subtle text-uppercase" style="font-size: 0.65rem;">
                                                    Prioridad <?= htmlspecialchars($t['priority']) ?>
                                                </span>
                                                <span style="font-size: 0.75rem;">
                                                    <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="index.php?action=toggle&id=<?= $t['id'] ?>&filter=<?= urlencode($filter) ?>" class="btn btn-sm <?= $isDone ? 'btn-outline-secondary' : 'btn-outline-success' ?>" title="Cambiar estado">
                                            <i class="bi <?= $isDone ? 'bi-arrow-counterclockwise' : 'bi-check2' ?>"></i>
                                        </a>
                                        <a href="index.php?action=delete&id=<?= $t['id'] ?>&filter=<?= urlencode($filter) ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           title="Eliminar tarea"
                                           onclick="return confirm('¿Deseas eliminar esta tarea?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../shared/footer.php'; ?>

