<?php
$pageTitle = '09 - Juego de Memoria';
$activeExercise = 9;
$depth = 1;
require __DIR__ . '/../../shared/header.php';
?>

<style>
/* 3D Flip Card Effect */
.memory-grid {
    display: grid;
    gap: 12px;
    perspective: 1000px;
}
.memory-card {
    height: 110px;
    position: relative;
    transform-style: preserve-3d;
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    user-select: none;
}
.memory-card.flipped {
    transform: rotateY(180deg);
}
.memory-card.matched {
    transform: rotateY(180deg);
    cursor: default;
}
.card-face {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
}
.card-back {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: 2px solid #2563eb;
}
.card-front {
    background: #ffffff;
    transform: rotateY(180deg);
    border: 2px solid #e2e8f0;
}
.memory-card.matched .card-front {
    border-color: #22c55e;
    background: #f0fdf4;
}
</style>

<div class="container py-2">
    <!-- Header Card -->
    <div class="card app-card mb-4 border-0">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="mvc-badge mb-2 d-inline-block">
                        <i class="bi bi-diagram-3-fill me-1"></i> Arquitectura MVC (Model: MemoryGame, View: game_view, Controller: GameController)
                    </span>
                    <h2 class="h3 fw-bold mb-1">
                        <i class="bi bi-grid-3x3-gap text-primary me-2"></i>Juego Clásico de Memoria
                    </h2>
                    <p class="text-muted mb-0 small">
                        Encuentra todos los pares de cartas antes de que se agote tu récord. Elige tu nivel de dificultad.
                    </p>
                </div>

                <!-- Difficulty Selector -->
                <div class="btn-group" role="group">
                    <?php foreach ($allLevels as $key => $lvl): ?>
                        <a href="index.php?difficulty=<?= $key ?>" class="btn <?= $difficulty === $key ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm px-3 fw-bold">
                            <?= $lvl['name'] ?> (<?= $lvl['pairs'] ?> pares)
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Game Status Bar -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card app-card border-0 p-3 text-center">
                <span class="text-muted small text-uppercase fw-semibold">Movimientos</span>
                <span class="fs-3 fw-bold text-dark mt-1" id="movesCounter">0</span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card app-card border-0 p-3 text-center">
                <span class="text-muted small text-uppercase fw-semibold">Tiempo</span>
                <span class="fs-3 fw-bold text-primary mt-1" id="timerDisplay">00:00</span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card app-card border-0 p-3 text-center">
                <span class="text-muted small text-uppercase fw-semibold">Pares Encontrados</span>
                <span class="fs-3 fw-bold text-success mt-1"><span id="pairsCounter">0</span> / <?= $levelInfo['pairs'] ?></span>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card app-card border-0 p-3 text-center">
                <span class="text-muted small text-uppercase fw-semibold">Puntaje Estimado</span>
                <span class="fs-3 fw-bold text-warning-emphasis mt-1" id="liveScore">1000</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Game Board Column -->
        <div class="col-lg-8">
            <div class="card app-card border-0 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Tablero de Juego (<?= $levelInfo['name'] ?>)</h5>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload();">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reiniciar
                    </button>
                </div>

                <div class="memory-grid" style="grid-template-columns: repeat(<?= $levelInfo['cols'] ?>, 1fr);" id="gameBoard">
                    <?php foreach ($cards as $c): ?>
                        <div class="memory-card" data-pair="<?= $c['pair_id'] ?>" data-uid="<?= $c['uid'] ?>">
                            <!-- Back (Hidden) -->
                            <div class="card-face card-back">
                                <i class="bi bi-question-lg fs-2"></i>
                            </div>
                            <!-- Front (Revealed) -->
                            <div class="card-face card-front">
                                <span style="font-size: 2.3rem;"><?= $c['icon'] ?></span>
                                <span class="small fw-bold text-muted mt-1" style="font-size: 0.72rem;"><?= htmlspecialchars($c['name']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Leaderboard Column -->
        <div class="col-lg-4">
            <div class="card app-card border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-trophy-fill text-warning me-2"></i>Mejores Puntajes (<?= $levelInfo['name'] ?>)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($leaderboard)): ?>
                        <div class="p-4 text-center text-muted small">
                            Aún no hay récords registrados en este nivel. ¡Sé el primero!
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Jugador</th>
                                        <th>Puntaje</th>
                                        <th>Tiempo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($leaderboard as $idx => $sc): ?>
                                        <tr>
                                            <td class="fw-bold text-muted"><?= $idx + 1 ?></td>
                                            <td class="fw-semibold text-dark"><?= htmlspecialchars($sc['player_name']) ?></td>
                                            <td class="fw-bold text-primary"><?= number_format($sc['score']) ?> pts</td>
                                            <td class="text-muted"><?= $sc['time_seconds'] ?>s (<?= $sc['moves'] ?> m)</td>
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

<!-- Victory Modal -->
<div class="modal fade" id="victoryModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body text-center p-5">
                <div class="display-1 text-warning mb-3">
                    <i class="bi bi-award-fill"></i>
                </div>
                <h3 class="fw-bold text-success mb-2">¡Felicitaciones, Ganaste!</h3>
                <p class="text-muted mb-4">Has completado el juego de memoria con éxito.</p>

                <div class="row g-2 mb-4 bg-light p-3 rounded-4">
                    <div class="col-4">
                        <small class="text-muted d-block">Movimientos</small>
                        <strong class="fs-5" id="modalMoves">0</strong>
                    </div>
                    <div class="col-4">
                        <small class="text-muted d-block">Tiempo</small>
                        <strong class="fs-5" id="modalTime">0s</strong>
                    </div>
                    <div class="col-4">
                        <small class="text-muted d-block">Puntaje</small>
                        <strong class="fs-5 text-primary" id="modalScore">0</strong>
                    </div>
                </div>

                <form action="index.php?action=save_score" method="POST">
                    <input type="hidden" name="difficulty" value="<?= htmlspecialchars($difficulty) ?>">
                    <input type="hidden" name="moves" id="inputMoves" value="0">
                    <input type="hidden" name="time_seconds" id="inputTime" value="0">
                    <input type="hidden" name="score" id="inputScore" value="0">

                    <div class="mb-3 text-start">
                        <label class="form-label small fw-bold">Escribe tu nombre para la tabla de líderes:</label>
                        <input type="text" name="player_name" class="form-control form-control-lg" placeholder="Tu nombre..." required autofocus>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-brand py-2 fs-5">
                            <i class="bi bi-save me-1"></i> Guardar Mi Récord
                        </button>
                        <a href="index.php?difficulty=<?= $difficulty ?>" class="btn btn-outline-secondary">
                            Jugar de Nuevo
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const totalPairs = <?= $levelInfo['pairs'] ?>;
let moves = 0;
let pairsFound = 0;
let timerSeconds = 0;
let timerInterval = null;
let gameStarted = false;
let flippedCards = [];
let lockBoard = false;

const movesCounter = document.getElementById('movesCounter');
const pairsCounter = document.getElementById('pairsCounter');
const timerDisplay = document.getElementById('timerDisplay');
const liveScore = document.getElementById('liveScore');
const victoryModal = new bootstrap.Modal(document.getElementById('victoryModal'));

function calculateScore() {
    const base = totalPairs * 1000;
    const penalty = (moves * 25) + (timerSeconds * 10);
    return Math.max(100, base - penalty);
}

function updateStats() {
    movesCounter.textContent = moves;
    pairsCounter.textContent = pairsFound;
    liveScore.textContent = calculateScore();
}

function startTimer() {
    if (timerInterval) return;
    timerInterval = setInterval(() => {
        timerSeconds++;
        const mins = String(Math.floor(timerSeconds / 60)).padStart(2, '0');
        const secs = String(timerSeconds % 60).padStart(2, '0');
        timerDisplay.textContent = `${mins}:${secs}`;
        liveScore.textContent = calculateScore();
    }, 1000);
}

function stopTimer() {
    clearInterval(timerInterval);
}

document.querySelectorAll('.memory-card').forEach(card => {
    card.addEventListener('click', function() {
        if (lockBoard) return;
        if (this === flippedCards[0]) return;
        if (this.classList.contains('matched') || this.classList.contains('flipped')) return;

        if (!gameStarted) {
            gameStarted = true;
            startTimer();
        }

        this.classList.add('flipped');
        flippedCards.push(this);

        if (flippedCards.length === 2) {
            moves++;
            updateStats();
            checkForMatch();
        }
    });
});

function checkForMatch() {
    const [card1, card2] = flippedCards;
    const isMatch = card1.dataset.pair === card2.dataset.pair;

    if (isMatch) {
        disableCards();
    } else {
        unflipCards();
    }
}

function disableCards() {
    flippedCards[0].classList.add('matched');
    flippedCards[1].classList.add('matched');
    pairsFound++;
    updateStats();
    resetBoard();

    if (pairsFound === totalPairs) {
        stopTimer();
        const finalScore = calculateScore();
        document.getElementById('modalMoves').textContent = moves;
        document.getElementById('modalTime').textContent = timerSeconds + 's';
        document.getElementById('modalScore').textContent = finalScore;
        
        document.getElementById('inputMoves').value = moves;
        document.getElementById('inputTime').value = timerSeconds;
        document.getElementById('inputScore').value = finalScore;

        setTimeout(() => {
            victoryModal.show();
        }, 500);
    }
}

function unflipCards() {
    lockBoard = true;
    setTimeout(() => {
        flippedCards[0].classList.remove('flipped');
        flippedCards[1].classList.remove('flipped');
        resetBoard();
    }, 900);
}

function resetBoard() {
    flippedCards = [];
    lockBoard = false;
}

updateStats();
</script>

<?php require __DIR__ . '/../../shared/footer.php'; ?>
