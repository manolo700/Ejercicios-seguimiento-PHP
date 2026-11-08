<?php
$pageTitle = '03 - Generador de Contraseñas Seguras';
$activeExercise = 3;
$depth = 1;
require __DIR__ . '/../../shared/header.php';
$opts = $result['options'];
?>

<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <!-- Header Card -->
            <div class="card app-card mb-4 border-0">
                <div class="card-body p-4">
                    <span class="mvc-badge mb-2 d-inline-block">
                        <i class="bi bi-diagram-3-fill me-1"></i> Arquitectura MVC (Model: PasswordGenerator, View: password_view, Controller: PasswordController)
                    </span>
                    <h2 class="h3 fw-bold mb-1">
                        <i class="bi bi-shield-lock text-primary me-2"></i>Generador de Contraseñas Criptoseguras
                    </h2>
                    <p class="text-muted mb-0 small">
                        Genera contraseñas aleatorias de alta entropía ajustando su longitud y tipos de caracteres permitidos.
                    </p>
                </div>
            </div>

            <!-- Main Password Display Card -->
            <div class="card app-card border-0 mb-4 shadow-sm" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
                <div class="card-body p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-white-50 text-uppercase fw-semibold tracking-wider">Contraseña Generada</span>
                        <span class="badge bg-<?= $result['strength']['color'] ?> px-3 py-1">
                            <?= $result['strength']['level'] ?>
                        </span>
                    </div>

                    <div class="input-group my-3">
                        <input type="text" id="primaryPassword" class="form-control form-control-lg bg-white text-dark font-monospace fw-bold fs-4 py-2" value="<?= htmlspecialchars($result['primary']) ?>" readonly>
                        <button class="btn btn-warning px-4 fw-bold d-flex align-items-center gap-2" type="button" id="btnCopyMain">
                            <i class="bi bi-clipboard"></i> <span id="btnCopyText">Copiar</span>
                        </button>
                    </div>

                    <!-- Strength Meter -->
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center small text-white-50 mb-1">
                            <span>Seguridad: <?= $result['strength']['description'] ?></span>
                            <span>Entropía: <?= $result['entropy'] ?> bits</span>
                        </div>
                        <div class="progress" style="height: 8px; background: rgba(255,255,255,0.2);">
                            <div class="progress-bar bg-<?= $result['strength']['color'] ?>" role="progressbar" style="width: <?= $result['strength']['percent'] ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuration Form -->
            <div class="card app-card border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-sliders me-2 text-primary"></i>Personalizar Parámetros
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="index.php" method="POST" id="configForm">
                        <!-- Length slider -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="lengthSlider" class="form-label fw-bold mb-0">Longitud de la contraseña</label>
                                <span class="badge bg-primary fs-6 px-3 py-1" id="lengthDisplay"><?= $result['length'] ?> caracteres</span>
                            </div>
                            <input type="range" class="form-range" min="6" max="64" value="<?= $result['length'] ?>" id="lengthSlider" name="length">
                            <div class="d-flex justify-content-between small text-muted">
                                <span>6 (Mínimo)</span>
                                <span>16 (Recomendado)</span>
                                <span>32 (Muy seguro)</span>
                                <span>64 (Ultra)</span>
                            </div>
                        </div>

                        <!-- Character Type Checkboxes -->
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-2">Tipos de Caracteres Permitidos</label>
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="form-check p-3 border rounded-3 bg-light">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="upper" value="1" id="checkUpper" <?= $opts['upper'] ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-medium" for="checkUpper">
                                            Mayúsculas <code class="small text-muted ms-1">(A-Z)</code>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check p-3 border rounded-3 bg-light">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="lower" value="1" id="checkLower" <?= $opts['lower'] ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-medium" for="checkLower">
                                            Minúsculas <code class="small text-muted ms-1">(a-z)</code>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check p-3 border rounded-3 bg-light">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="numbers" value="1" id="checkNumbers" <?= $opts['numbers'] ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-medium" for="checkNumbers">
                                            Números <code class="small text-muted ms-1">(0-9)</code>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-check p-3 border rounded-3 bg-light">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="symbols" value="1" id="checkSymbols" <?= $opts['symbols'] ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-medium" for="checkSymbols">
                                            Símbolos <code class="small text-muted ms-1">(!@#$%^&*)</code>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced options -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <div class="form-check form-switch pt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" name="exclude_ambiguous" value="1" id="checkAmbiguous" <?= $opts['exclude_ambiguous'] ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="checkAmbiguous">
                                        Excluir caracteres ambiguos <code class="small text-muted">(0, O, o, 1, l, I)</code>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-2">
                                    <label for="quantitySelect" class="form-label small fw-bold mb-0 text-nowrap">Generar varias:</label>
                                    <select class="form-select form-select-sm" name="quantity" id="quantitySelect">
                                        <option value="1" <?= $opts['quantity'] == 1 ? 'selected' : '' ?>>1 contraseña</option>
                                        <option value="3" <?= $opts['quantity'] == 3 ? 'selected' : '' ?>>3 alternativas</option>
                                        <option value="5" <?= $opts['quantity'] == 5 ? 'selected' : '' ?>>5 alternativas</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-brand py-2 fs-5">
                                <i class="bi bi-arrow-repeat me-2"></i> Generar Nueva Contraseña
                            </button>
                        </div>
                    </form>

                    <!-- Multiple Generated Passwords if selected -->
                    <?php if (count($result['passwords']) > 1): ?>
                        <div class="mt-4 border-top pt-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-list-check me-2"></i>Alternativas Generadas:</h6>
                            <div class="list-group">
                                <?php foreach ($result['passwords'] as $idx => $pwd): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                        <span class="font-monospace fw-medium text-dark"><?= htmlspecialchars($pwd) ?></span>
                                        <button class="btn btn-sm btn-outline-secondary copy-alt-btn" data-password="<?= htmlspecialchars($pwd) ?>">
                                            <i class="bi bi-clipboard"></i> Copiar
                                        </button>
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

<script>
const lengthSlider = document.getElementById('lengthSlider');
const lengthDisplay = document.getElementById('lengthDisplay');

lengthSlider.addEventListener('input', function() {
    lengthDisplay.textContent = this.value + ' caracteres';
});

// Copy primary password
const btnCopyMain = document.getElementById('btnCopyMain');
const primaryPassword = document.getElementById('primaryPassword');
const btnCopyText = document.getElementById('btnCopyText');

btnCopyMain.addEventListener('click', function() {
    navigator.clipboard.writeText(primaryPassword.value).then(() => {
        btnCopyText.textContent = '¡Copiado!';
        btnCopyMain.classList.replace('btn-warning', 'btn-success');
        setTimeout(() => {
            btnCopyText.textContent = 'Copiar';
            btnCopyMain.classList.replace('btn-success', 'btn-warning');
        }, 2000);
    });
});

// Copy alternatives
document.querySelectorAll('.copy-alt-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const text = this.dataset.password;
        navigator.clipboard.writeText(text).then(() => {
            const original = this.innerHTML;
            this.innerHTML = '<i class="bi bi-check2"></i> ¡Listo!';
            this.classList.replace('btn-outline-secondary', 'btn-success');
            setTimeout(() => {
                this.innerHTML = original;
                this.classList.replace('btn-success', 'btn-outline-secondary');
            }, 1800);
        });
    });
});
</script>

<?php require __DIR__ . '/../../shared/footer.php'; ?>
