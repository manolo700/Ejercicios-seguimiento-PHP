<?php
$pageTitle = '08 - Plataforma de Recetas';
$activeExercise = 8;
$depth = 1;
require __DIR__ . '/../../shared/header.php';
?>

<div class="container py-2">
    <!-- Header Card -->
    <div class="card app-card mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="mvc-badge mb-2 d-inline-block">
                        <i class="bi bi-diagram-3-fill me-1"></i> Arquitectura MVC (Model: Recipe, View: recipe_view, Controller: RecipeController)
                    </span>
                    <h2 class="h3 fw-bold mb-1">
                        <i class="bi bi-egg-fried text-primary me-2"></i>Plataforma Culinaria & Recetas de Cocina
                    </h2>
                    <p class="text-muted mb-0 small">
                        Descubre, filtra por tipo de comida o ingredientes, comparte tus creaciones y disfruta de buena cocina.
                    </p>
                </div>
                <div>
                    <button class="btn btn-brand px-3 py-2 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#newRecipeModal">
                        <i class="bi bi-plus-circle"></i>
                        <span>Publicar Receta</span>
                    </button>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="row g-2 mt-3 pt-3 border-top">
                <div class="col-md-5">
                    <form action="index.php" method="GET" class="input-group">
                        <?php if (!empty($mealType)): ?>
                            <input type="hidden" name="meal_type" value="<?= htmlspecialchars($mealType) ?>">
                        <?php endif; ?>
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Buscar por título, ingrediente..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                    </form>
                </div>
                <div class="col-md-7 d-flex flex-wrap gap-1 align-items-center justify-content-md-end">
                    <a href="index.php" class="btn btn-sm <?= empty($mealType) ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill">
                        Todas las Categorías
                    </a>
                    <?php foreach ($mealTypes as $mt): ?>
                        <a href="index.php?meal_type=<?= urlencode($mt) ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                           class="btn btn-sm <?= $mealType === $mt ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill">
                            <?= $mt ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recipe Grid -->
    <?php if (empty($recipes)): ?>
        <div class="card app-card border-0 text-center py-5">
            <div class="card-body">
                <i class="bi bi-search display-3 text-muted"></i>
                <h5 class="mt-3 text-secondary">No se encontraron recetas</h5>
                <p class="text-muted small">Intenta buscar con otros términos o crea una receta nueva.</p>
                <button class="btn btn-brand btn-sm" data-bs-toggle="modal" data-bs-target="#newRecipeModal">
                    <i class="bi bi-plus-lg me-1"></i> Publicar una Receta
                </button>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($recipes as $r): ?>
                <?php 
                    $diffClass = match($r['difficulty']) {
                        'Fácil' => 'success',
                        'Media' => 'warning',
                        'Avanzada' => 'danger',
                        default => 'secondary'
                    };
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card app-card border-0 h-100 overflow-hidden d-flex flex-column justify-content-between">
                        <div>
                            <!-- Recipe Image Banner -->
                            <div class="position-relative" style="height: 180px; overflow: hidden; background: #e2e8f0;">
                                <img src="<?= htmlspecialchars($r['image_url']) ?>" alt="<?= htmlspecialchars($r['title']) ?>" class="w-100 h-100" style="object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=600&auto=format&fit=crop&q=80'">
                                <span class="badge bg-dark bg-opacity-75 position-absolute top-0 end-0 m-2">
                                    <i class="bi bi-clock me-1"></i><?= $r['prep_time_minutes'] ?> min
                                </span>
                                <span class="badge bg-primary position-absolute bottom-0 start-0 m-2">
                                    <?= htmlspecialchars($r['meal_type']) ?>
                                </span>
                            </div>

                            <div class="p-4 pb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-<?= $diffClass ?>-subtle text-<?= $diffClass ?> border border-<?= $diffClass ?>-subtle" style="font-size: 0.7rem;">
                                        Dificultad: <?= htmlspecialchars($r['difficulty']) ?>
                                    </span>
                                    <span class="small text-muted">
                                        <i class="bi bi-people me-1"></i><?= $r['servings'] ?> porciones
                                    </span>
                                </div>
                                <h5 class="fw-bold text-dark mt-2 mb-1">
                                    <?= htmlspecialchars($r['title']) ?>
                                </h5>
                                <p class="text-muted small mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <?= htmlspecialchars($r['description']) ?>
                                </p>
                            </div>
                        </div>

                        <div class="p-4 pt-0">
                            <hr class="my-2 text-muted opacity-25">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted" style="font-size: 0.75rem;">
                                    Por <?= htmlspecialchars($r['author_name']) ?>
                                </span>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary view-recipe-btn"
                                            data-title="<?= htmlspecialchars($r['title']) ?>"
                                            data-desc="<?= htmlspecialchars($r['description']) ?>"
                                            data-img="<?= htmlspecialchars($r['image_url']) ?>"
                                            data-type="<?= htmlspecialchars($r['meal_type']) ?>"
                                            data-diff="<?= htmlspecialchars($r['difficulty']) ?>"
                                            data-time="<?= $r['prep_time_minutes'] ?>"
                                            data-servings="<?= $r['servings'] ?>"
                                            data-author="<?= htmlspecialchars($r['author_name']) ?>"
                                            data-ingredients="<?= htmlspecialchars($r['ingredients']) ?>"
                                            data-instructions="<?= htmlspecialchars($r['instructions']) ?>">
                                        <i class="bi bi-book-half me-1"></i> Ver Receta
                                    </button>
                                    <a href="index.php?action=delete&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar esta receta?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal View Recipe Details -->
<div class="modal fade" id="viewRecipeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="viewRecipeTitle">Título de Receta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4 mb-3">
                    <div class="col-md-5">
                        <img id="viewRecipeImg" src="" alt="Receta" class="img-fluid rounded-3 shadow-sm w-100" style="max-height: 240px; object-fit: cover;">
                        <div class="mt-3 p-3 bg-light rounded-3 small">
                            <div class="d-flex justify-content-between mb-1">
                                <strong>Categoría:</strong> <span id="viewRecipeType"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <strong>Dificultad:</strong> <span id="viewRecipeDiff"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <strong>Tiempo:</strong> <span id="viewRecipeTime"></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <strong>Porciones:</strong> <span id="viewRecipeServings"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <p class="text-muted" id="viewRecipeDesc"></p>
                        <h6 class="fw-bold text-primary mt-3"><i class="bi bi-basket me-2"></i>Ingredientes:</h6>
                        <ul class="list-group list-group-flush small" id="viewRecipeIngredientsList"></ul>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <h5 class="fw-bold text-primary mb-3"><i class="bi bi-list-ol me-2"></i>Instrucciones de Preparación:</h5>
                    <div id="viewRecipeInstructions" class="p-3 bg-light rounded-3" style="white-space: pre-wrap; line-height: 1.6;"></div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create Recipe -->
<div class="modal fade" id="newRecipeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle text-primary me-2"></i>Publicar Nueva Receta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=create" method="POST">
                <div class="modal-body p-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">Nombre del Platillo</label>
                            <input type="text" name="title" class="form-control" placeholder="Ej. Risotto de Champiñones" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Autor</label>
                            <input type="text" name="author_name" class="form-control" placeholder="Tu nombre" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Descripción Breve</label>
                        <input type="text" name="description" class="form-control" placeholder="Una descripción atractiva para los comensales..." required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tipo de Comida</label>
                            <select name="meal_type" class="form-select">
                                <?php foreach ($mealTypes as $mt): ?>
                                    <option value="<?= $mt ?>"><?= $mt ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Dificultad</label>
                            <select name="difficulty" class="form-select">
                                <option value="Fácil">Fácil</option>
                                <option value="Media" selected>Media</option>
                                <option value="Avanzada">Avanzada</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Tiempo (min)</label>
                            <input type="number" name="prep_time_minutes" class="form-control" value="30" min="5" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Porciones</label>
                            <input type="number" name="servings" class="form-control" value="4" min="1" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">URL de Imagen (Opcional)</label>
                        <input type="url" name="image_url" class="form-control" placeholder="https://ejemplo.com/foto.jpg">
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Ingredientes (uno por línea)</label>
                            <textarea name="ingredients" rows="5" class="form-control" placeholder="1 taza de arroz&#10;2 tazas de caldo&#10;Sal al gusto..." required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Instrucciones Paso a Paso</label>
                            <textarea name="instructions" rows="5" class="form-control" placeholder="1. Calentar el aceite...&#10;2. Dorar los ingredientes...&#10;3. Servir caliente." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-brand">Publicar Receta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('viewRecipeModal');
    if (!modalElement) return;

    const viewRecipeModal = new bootstrap.Modal(modalElement);
    document.querySelectorAll('.view-recipe-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('viewRecipeTitle').textContent = this.dataset.title;
            document.getElementById('viewRecipeDesc').textContent = this.dataset.desc;
            document.getElementById('viewRecipeImg').src = this.dataset.img;
            document.getElementById('viewRecipeType').textContent = this.dataset.type;
            document.getElementById('viewRecipeDiff').textContent = this.dataset.diff;
            document.getElementById('viewRecipeTime').textContent = this.dataset.time + ' minutos';
            document.getElementById('viewRecipeServings').textContent = this.dataset.servings + ' personas';
            document.getElementById('viewRecipeInstructions').textContent = this.dataset.instructions;

            const ingList = document.getElementById('viewRecipeIngredientsList');
            if (ingList) {
                ingList.innerHTML = '';
                const rawIng = (this.dataset.ingredients || '').split('\n');
                rawIng.forEach(line => {
                    if (line.trim().length > 0) {
                        const li = document.createElement('li');
                        li.className = 'list-group-item px-0 py-1 bg-transparent';
                        li.innerHTML = '<i class="bi bi-check2 text-success me-2"></i>' + line.trim();
                        ingList.appendChild(li);
                    }
                });
            }

            viewRecipeModal.show();
        });
    });
});
</script>

<?php require __DIR__ . '/../../shared/footer.php'; ?>

