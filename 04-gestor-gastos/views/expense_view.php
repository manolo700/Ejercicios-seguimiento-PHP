<?php
$pageTitle = '04 - Gestor de Gastos';
$activeExercise = 4;
$depth = 1;
require __DIR__ . '/../../shared/header.php';

$meses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];
?>

<div class="container py-2">
    <!-- Header Card -->
    <div class="card app-card mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="mvc-badge mb-2 d-inline-block">
                        <i class="bi bi-diagram-3-fill me-1"></i> Arquitectura MVC (Model: Expense, View: expense_view, Controller: ExpenseController)
                    </span>
                    <h2 class="h3 fw-bold mb-1">
                        <i class="bi bi-cash-coin text-primary me-2"></i>Gestor de Gastos y Finanzas Personales
                    </h2>
                    <p class="text-muted mb-0 small">
                        Registra gastos diarios por categoría, monitorea resúmenes mensuales y controla tu presupuesto.
                    </p>
                </div>
                <!-- Month Filter Form -->
                <form action="index.php" method="GET" class="d-flex align-items-center gap-2">
                    <select name="month" class="form-select form-select-sm fw-semibold">
                        <?php foreach ($meses as $num => $nom): ?>
                            <option value="<?= $num ?>" <?= $month == $num ? 'selected' : '' ?>><?= $nom ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="year" class="form-select form-select-sm fw-semibold">
                        <?php for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++): ?>
                            <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Monthly Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card app-card border-0 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Gasto Total Mensual</div>
                        <div class="fs-4 fw-bold text-primary mt-1">$<?= number_format($stats['total_amount'], 2) ?></div>
                    </div>
                    <div class="p-3 rounded-circle bg-primary-subtle text-primary">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card app-card border-0 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Promedio Diario</div>
                        <div class="fs-4 fw-bold text-dark mt-1">$<?= number_format($stats['daily_average'], 2) ?></div>
                    </div>
                    <div class="p-3 rounded-circle bg-info-subtle text-info">
                        <i class="bi bi-graph-up fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card app-card border-0 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Gasto Mayor</div>
                        <div class="fs-4 fw-bold text-danger mt-1">$<?= number_format($stats['max_amount'], 2) ?></div>
                    </div>
                    <div class="p-3 rounded-circle bg-danger-subtle text-danger">
                        <i class="bi bi-arrow-up-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card app-card border-0 p-3 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">Total Transacciones</div>
                        <div class="fs-4 fw-bold text-success mt-1"><?= $stats['total_count'] ?></div>
                    </div>
                    <div class="p-3 rounded-circle bg-success-subtle text-success">
                        <i class="bi bi-receipt-cutoff fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Add Expense Form Column -->
        <div class="col-lg-4">
            <div class="card app-card border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-plus-circle text-primary me-2"></i>Registrar Gasto
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="index.php?action=add" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Descripción del Gasto</label>
                            <input type="text" name="title" class="form-control" placeholder="Ej. Almuerzo de trabajo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Monto ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Categoría</label>
                            <select name="category" class="form-select" required>
                                <?php foreach ($categories as $catName => $catMeta): ?>
                                    <option value="<?= $catName ?>"><?= $catName ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Fecha del Gasto</label>
                            <input type="date" name="expense_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold">Notas Adicionales (Opcional)</label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="Detalles extra..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-brand w-100 py-2">
                            <i class="bi bi-save me-1"></i> Guardar Gasto
                        </button>
                    </form>
                </div>
            </div>

            <!-- Categories Distribution Progress -->
            <div class="card app-card border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-pie-chart text-primary me-2"></i>Distribución por Categoría
                    </h5>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($categoryBreakdown)): ?>
                        <p class="text-muted small text-center my-3">Sin gastos registrados este mes.</p>
                    <?php else: ?>
                        <?php foreach ($categoryBreakdown as $cat): ?>
                            <?php 
                                $catPct = $stats['total_amount'] > 0 ? ($cat['total'] / $stats['total_amount']) * 100 : 0;
                            ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small fw-bold mb-1">
                                    <span><?= htmlspecialchars($cat['category']) ?> (<?= $cat['count'] ?>)</span>
                                    <span>$<?= number_format($cat['total'], 2) ?> (<?= round($catPct, 1) ?>%)</span>
                                </div>
                                <div class="progress" style="height: 7px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $catPct ?>%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Expense Table Column -->
        <div class="col-lg-8">
            <div class="card app-card border-0">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-list-columns-reverse text-primary me-2"></i>Gastos de <?= $meses[$month] ?> <?= $year ?>
                    </h5>
                    <span class="badge bg-light text-dark border"><?= count($expenses) ?> registros</span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($expenses)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-4"></i>
                            <h5 class="mt-3">No hay gastos para este mes</h5>
                            <p class="small">Utiliza el formulario de la izquierda para agregar tu primer gasto.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small text-uppercase">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Concepto</th>
                                        <th>Categoría</th>
                                        <th>Monto</th>
                                        <th class="text-end">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($expenses as $item): ?>
                                        <?php 
                                            $catInfo = $categories[$item['category']] ?? ['icon' => 'bi-tag', 'color' => 'secondary'];
                                        ?>
                                        <tr>
                                            <td class="text-muted small text-nowrap">
                                                <?= date('d/m/Y', strtotime($item['expense_date'])) ?>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark"><?= htmlspecialchars($item['title']) ?></div>
                                                <?php if (!empty($item['notes'])): ?>
                                                    <div class="small text-muted"><?= htmlspecialchars($item['notes']) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border d-inline-flex align-items-center gap-1">
                                                    <i class="bi <?= $catInfo['icon'] ?> text-primary"></i>
                                                    <?= htmlspecialchars($item['category']) ?>
                                                </span>
                                            </td>
                                            <td class="fw-bold text-dark text-nowrap">
                                                $<?= number_format($item['amount'], 2) ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="index.php?action=delete&id=<?= $item['id'] ?>&year=<?= $year ?>&month=<?= $month ?>" 
                                                   class="btn btn-sm btn-outline-danger" 
                                                   title="Eliminar gasto"
                                                   onclick="return confirm('¿Seguro que deseas eliminar este gasto?');">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../shared/footer.php'; ?>

