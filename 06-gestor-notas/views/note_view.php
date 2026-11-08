<?php
$pageTitle = '06 - Gestor de Notas';
$activeExercise = 6;
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
                        <i class="bi bi-diagram-3-fill me-1"></i> Arquitectura MVC (Model: Note, View: note_view, Controller: NoteController)
                    </span>
                    <h2 class="h3 fw-bold mb-1">
                        <i class="bi bi-journal-text text-primary me-2"></i>Gestor y Organizador de Notas
                    </h2>
                    <p class="text-muted mb-0 small">
                        Escribe, organiza por categorías, fija notas importantes y busca rápidamente en tu libreta digital.
                    </p>
                </div>
                <div>
                    <button class="btn btn-brand px-3 py-2 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#noteModal" id="btnNewNote">
                        <i class="bi bi-plus-lg"></i>
                        <span>Nueva Nota</span>
                    </button>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="row g-2 mt-3 pt-3 border-top">
                <div class="col-md-6">
                    <form action="index.php" method="GET" class="input-group">
                        <?php if (!empty($category)): ?>
                            <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                        <?php endif; ?>
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Buscar por título o contenido..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                        <?php if (!empty($search)): ?>
                            <a href="index.php<?= !empty($category) ? '?category=' . urlencode($category) : '' ?>" class="btn btn-outline-danger">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="col-md-6 d-flex flex-wrap gap-1 align-items-center justify-content-md-end">
                    <a href="index.php<?= !empty($search) ? '?search=' . urlencode($search) : '' ?>" 
                       class="btn btn-sm <?= empty($category) ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill">
                        Todas
                    </a>
                    <?php foreach ($categories as $catName => $catMeta): ?>
                        <?php $count = $categoryStats[$catName] ?? 0; ?>
                        <a href="index.php?category=<?= urlencode($catName) ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                           class="btn btn-sm <?= $category === $catName ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill">
                            <?= $catName ?> <span class="badge bg-light text-dark ms-1"><?= $count ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes Grid -->
    <?php if (empty($notes)): ?>
        <div class="card app-card border-0 text-center py-5">
            <div class="card-body">
                <i class="bi bi-stickies display-3 text-muted"></i>
                <h5 class="mt-3 text-secondary">No se encontraron notas</h5>
                <p class="text-muted small">Crea una nueva nota o ajusta tu búsqueda.</p>
                <button class="btn btn-brand btn-sm" data-bs-toggle="modal" data-bs-target="#noteModal">
                    <i class="bi bi-plus-lg me-1"></i> Crear mi primera nota
                </button>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($notes as $n): ?>
                <?php 
                    $colorKey = $n['color'] ?? 'yellow';
                    $colorStyle = Note::COLORS[$colorKey] ?? Note::COLORS['yellow'];
                    $catInfo = Note::CATEGORIES[$n['category']] ?? ['color' => 'secondary', 'icon' => 'bi-tag'];
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 h-100 shadow-sm rounded-4 position-relative" 
                         style="background-color: <?= $colorStyle['bg'] ?>; border-left: 6px solid <?= $colorStyle['border'] ?> !important;">
                        
                        <!-- Card Header Actions -->
                        <div class="p-3 pb-0 d-flex justify-content-between align-items-center">
                            <span class="badge rounded-pill bg-white text-dark border small">
                                <i class="bi <?= $catInfo['icon'] ?> text-primary me-1"></i><?= htmlspecialchars($n['category']) ?>
                            </span>
                            <div class="d-flex align-items-center gap-1">
                                <a href="index.php?action=toggle_pin&id=<?= $n['id'] ?>" class="btn btn-sm text-decoration-none p-1 <?= $n['is_pinned'] ? 'text-danger' : 'text-muted' ?>" title="<?= $n['is_pinned'] ? 'Desanclar nota' : 'Fijar nota arriba' ?>">
                                    <i class="bi <?= $n['is_pinned'] ? 'bi-pin-angle-fill' : 'bi-pin-angle' ?> fs-5"></i>
                                </a>
                                <button type="button" class="btn btn-sm text-muted p-1 edit-note-btn" 
                                        data-id="<?= $n['id'] ?>"
                                        data-title="<?= htmlspecialchars($n['title']) ?>"
                                        data-content="<?= htmlspecialchars($n['content']) ?>"
                                        data-category="<?= htmlspecialchars($n['category']) ?>"
                                        data-color="<?= htmlspecialchars($n['color']) ?>"
                                        data-pinned="<?= $n['is_pinned'] ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <a href="index.php?action=delete&id=<?= $n['id'] ?>" class="btn btn-sm text-muted p-1 hover-danger" onclick="return confirm('¿Eliminar esta nota?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-3 pt-2 d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="fw-bold mb-2" style="color: <?= $colorStyle['text'] ?>;">
                                    <?= htmlspecialchars($n['title']) ?>
                                </h5>
                                <p class="text-dark mb-3" style="white-space: pre-wrap; font-size: 0.92rem; line-height: 1.5;">
                                    <?= htmlspecialchars($n['content']) ?>
                                </p>
                            </div>
                            <div class="small text-muted pt-2 border-top d-flex justify-content-between align-items-center" style="font-size: 0.75rem; border-color: rgba(0,0,0,0.08) !important;">
                                <span><i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($n['updated_at'])) ?></span>
                                <?php if ($n['is_pinned']): ?>
                                    <span class="badge bg-danger-subtle text-danger"><i class="bi bi-pin-fill"></i> Fijada</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Create / Edit Note -->
<div class="modal fade" id="noteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalTitle">
                    <i class="bi bi-journal-plus text-primary me-2"></i>Nueva Nota
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?action=save" method="POST" id="noteForm">
                <input type="hidden" name="id" id="noteId" value="0">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Título</label>
                        <input type="text" name="title" id="noteTitle" class="form-control" placeholder="Título de la nota..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Contenido</label>
                        <textarea name="content" id="noteContent" rows="4" class="form-control" placeholder="Escribe tu contenido aquí..." required></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold">Categoría</label>
                            <select name="category" id="noteCategory" class="form-select">
                                <?php foreach ($categories as $catKey => $catInfo): ?>
                                    <option value="<?= $catKey ?>"><?= $catKey ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-bold">Color del Post-it</label>
                            <select name="color" id="noteColor" class="form-select">
                                <option value="yellow">Amarillo Claro</option>
                                <option value="blue">Azul Suave</option>
                                <option value="green">Verde Menta</option>
                                <option value="purple">Morado Lavanda</option>
                                <option value="red">Rojo Coral</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" name="is_pinned" id="notePinned">
                        <label class="form-check-label small" for="notePinned">Fijar esta nota al principio</label>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-brand">Guardar Nota</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const noteModal = new bootstrap.Modal(document.getElementById('noteModal'));
const modalTitle = document.getElementById('modalTitle');
const noteId = document.getElementById('noteId');
const noteTitle = document.getElementById('noteTitle');
const noteContent = document.getElementById('noteContent');
const noteCategory = document.getElementById('noteCategory');
const noteColor = document.getElementById('noteColor');
const notePinned = document.getElementById('notePinned');

document.getElementById('btnNewNote').addEventListener('click', function() {
    modalTitle.innerHTML = '<i class="bi bi-journal-plus text-primary me-2"></i>Nueva Nota';
    noteId.value = 0;
    noteTitle.value = '';
    noteContent.value = '';
    noteCategory.value = 'Personal';
    noteColor.value = 'yellow';
    notePinned.checked = false;
});

document.querySelectorAll('.edit-note-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        modalTitle.innerHTML = '<i class="bi bi-pencil-square text-primary me-2"></i>Editar Nota';
        noteId.value = this.dataset.id;
        noteTitle.value = this.dataset.title;
        noteContent.value = this.dataset.content;
        noteCategory.value = this.dataset.category;
        noteColor.value = this.dataset.color;
        notePinned.checked = this.dataset.pinned === '1';
        noteModal.show();
    });
});
</script>

<?php require __DIR__ . '/../../shared/footer.php'; ?>
