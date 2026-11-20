<?php
$pageTitle = '02 - Tip Calculator';
$activeExercise = 2;
$depth = 1;
require __DIR__ . '/../../shared/header.php';
?>

<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <!-- Header Card -->
            <div class="card app-card mb-4 border-0">
                <div class="card-body p-4">
                    <span class="mvc-badge mb-2 d-inline-block">
                        <i class="bi bi-diagram-3-fill me-1"></i> MVC Architecture (Model: TipCalculator, View: tip_view, Controller: TipController)
                    </span>
                    <h2 class="h3 fw-bold mb-1">
                        <i class="bi bi-calculator text-primary me-2"></i>Smart Tip Calculator
                    </h2>
                    <p class="text-muted mb-0 small">
                        Automatically calculates the tip based on the bill amount and selected percentage, with bill splitting and rounding options.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                <!-- Input Form -->
                <div class="col-lg-6">
                    <div class="card app-card border-0 h-100">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="card-title mb-0 fw-semibold">
                                <i class="bi bi-receipt text-primary me-2"></i>Bill Information
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="index.php" method="POST" id="tipForm">
                                <!-- Bill Amount -->
                                <div class="mb-3">
                                    <label class="form-label fw-medium small text-muted">TOTAL BILL AMOUNT ($)</label>
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light text-muted fw-bold">$</span>
                                        <input type="number" step="0.01" min="0.01" name="bill_amount" id="bill_amount" 
                                               class="form-control fw-bold" placeholder="0.00" 
                                               value="<?= $billAmount > 0 ? htmlspecialchars($billAmount) : '' ?>" required autofocus>
                                    </div>
                                </div>

                                <!-- Tip Percent Selection -->
                                <div class="mb-3">
                                    <label class="form-label fw-medium small text-muted">SELECT TIP PERCENTAGE</label>
                                    <div class="row g-2 mb-2">
                                        <?php foreach ([10, 15, 18, 20] as $pct): ?>
                                            <div class="col-3">
                                                <button type="button" class="btn btn-outline-primary w-100 py-2 fw-semibold tip-preset <?= ($tipPercent == $pct) ? 'active' : '' ?>" data-value="<?= $pct ?>">
                                                    <?= $pct ?>%
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted small">Custom</span>
                                        <input type="number" step="0.5" min="0" max="100" name="tip_percent" id="tip_percent" 
                                               class="form-control" value="<?= htmlspecialchars($tipPercent) ?>">
                                        <span class="input-group-text bg-light">%</span>
                                    </div>
                                </div>

                                <!-- Split Bill -->
                                <div class="mb-3">
                                    <label class="form-label fw-medium small text-muted">SPLIT BETWEEN PEOPLE</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="bi bi-people-fill text-muted"></i></span>
                                        <input type="number" min="1" max="100" name="num_people" id="num_people" class="form-control" value="<?= htmlspecialchars($numPeople) ?>">
                                        <span class="input-group-text bg-light">person(s)</span>
                                    </div>
                                </div>

                                <!-- Round up checkbox -->
                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" role="switch" name="round_up" value="1" id="roundUpSwitch" <?= $roundUp ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="roundUpSwitch">
                                        Round up total to next whole dollar
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-brand w-100 py-2 fs-5">
                                    <i class="bi bi-calculator me-1"></i> Calculate Tip
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Results Column -->
                <div class="col-lg-6">
                    <div class="card app-card border-0 h-100 <?= $calculated ? 'border-primary' : '' ?>" style="background: linear-gradient(180deg, #ffffff 0%, #f8faff 100%);">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h5 class="card-title mb-0 fw-semibold text-primary">
                                <i class="bi bi-cash-stack me-2"></i>Calculation Summary
                            </h5>
                        </div>
                        <div class="card-body p-4 d-flex flex-column justify-content-between">
                            <?php if ($calculated && $calculation): ?>
                                <div>
                                    <div class="row g-3 mb-4">
                                        <div class="col-6">
                                            <div class="p-3 bg-white rounded-3 border">
                                                <div class="text-muted small">Calculated Tip (<?= $calculation['tip_percent'] ?>%)</div>
                                                <div class="fs-4 fw-bold text-primary mt-1">
                                                    $<?= number_format($calculation['tip_amount'], 2) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="p-3 bg-white rounded-3 border">
                                                <div class="text-muted small">Original Bill</div>
                                                <div class="fs-4 fw-bold text-dark mt-1">
                                                    $<?= number_format($calculation['bill_amount'], 2) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-4 bg-primary text-white rounded-4 shadow-sm mb-4 text-center">
                                        <div class="small text-uppercase text-white-50 fw-semibold">Total to Pay (Bill + Tip)</div>
                                        <div class="display-5 fw-bold my-1">
                                            $<?= number_format($calculation['total_amount'], 2) ?>
                                        </div>
                                        <?php if ($calculation['num_people'] > 1): ?>
                                            <hr class="border-white-50 my-2">
                                            <div class="d-flex justify-content-around mt-2">
                                                <div>
                                                    <span class="small text-white-50">Per person:</span>
                                                    <div class="fs-5 fw-semibold">$<?= number_format($calculation['total_per_person'], 2) ?></div>
                                                </div>
                                                <div>
                                                    <span class="small text-white-50">Tip/person:</span>
                                                    <div class="fs-5 fw-semibold">$<?= number_format($calculation['tip_per_person'], 2) ?></div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5 my-auto text-muted">
                                    <i class="bi bi-wallet2 display-3 opacity-50"></i>
                                    <h5 class="mt-3">Enter the amount to calculate</h5>
                                    <p class="small">The detailed receipt breakdown will appear here.</p>
                                </div>
                            <?php endif; ?>

                            <!-- Recent calculations -->
                            <?php if (!empty($history)): ?>
                                <div class="mt-3 border-top pt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small fw-bold text-muted">Recent History:</span>
                                        <a href="index.php?action=clear_history" class="small text-danger text-decoration-none">
                                            <i class="bi bi-trash"></i> Clear
                                        </a>
                                    </div>
                                    <div class="list-group list-group-flush small">
                                        <?php foreach ($history as $h): ?>
                                            <div class="list-group-item bg-transparent px-0 py-1 d-flex justify-content-between align-items-center text-muted">
                                                <span>Bill $<?= number_format($h['bill_amount'], 2) ?> (+<?= $h['tip_percent'] ?>%)</span>
                                                <span class="fw-bold text-dark">$<?= number_format($h['total_amount'], 2) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tip-preset').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tip-preset').forEach(function(b) {
                b.classList.remove('active');
            });
            this.classList.add('active');
            const tipPercentInput = document.getElementById('tip_percent');
            if (tipPercentInput) {
                tipPercentInput.value = this.dataset.value;
            }
        });
    });
});
</script>

<?php require __DIR__ . '/../../shared/footer.php'; ?>
