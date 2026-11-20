<?php
$pageTitle = 'PHP MVC Exercises Portfolio';
$activeExercise = 0;
$depth = 0;
require __DIR__ . '/shared/header.php';

$exercisesList = [
    [
        'id' => 1,
        'title' => 'Task List',
        'folder' => '01-task-list',
        'icon' => 'bi-check2-square',
        'color' => 'primary',
        'desc' => 'Interactive web application allowing users to create, delete, and mark tasks as completed with status filtering and SQLite database persistence.',
        'model' => 'Task',
        'controller' => 'TaskController',
        'view' => 'task_view.php'
    ],
    [
        'id' => 2,
        'title' => 'Tip Calculator',
        'folder' => '02-tip-calculator',
        'icon' => 'bi-calculator',
        'color' => 'success',
        'desc' => 'Automatically calculates tip amount, total bill with tip, per-person split for shared dining, rounding options, and stores recent calculation history.',
        'model' => 'TipCalculator',
        'controller' => 'TipController',
        'view' => 'tip_view.php'
    ],
    [
        'id' => 3,
        'title' => 'Password Generator',
        'folder' => '03-password-generator',
        'icon' => 'bi-shield-lock',
        'color' => 'warning',
        'desc' => 'Cryptographically secure password generator with customizable length, character sets (uppercase, lowercase, numbers, symbols), entropy score, and clipboard copy.',
        'model' => 'PasswordGenerator',
        'controller' => 'PasswordController',
        'view' => 'password_view.php'
    ],
    [
        'id' => 4,
        'title' => 'Expense Manager',
        'folder' => '04-expense-manager',
        'icon' => 'bi-cash-coin',
        'color' => 'danger',
        'desc' => 'Track daily expenses by category, view monthly summaries, daily averages, highest expense indicators, and visual percentage breakdowns.',
        'model' => 'Expense',
        'controller' => 'ExpenseController',
        'view' => 'expense_view.php'
    ],
    [
        'id' => 5,
        'title' => 'Booking System',
        'folder' => '05-booking-system',
        'icon' => 'bi-calendar-check',
        'color' => 'info',
        'desc' => 'Online appointment and service reservation system featuring real-time schedule slot availability checks and digital confirmation vouchers.',
        'model' => 'Booking',
        'controller' => 'BookingController',
        'view' => 'booking_view.php'
    ],
    [
        'id' => 6,
        'title' => 'Notes Manager',
        'folder' => '06-notes-manager',
        'icon' => 'bi-journal-text',
        'color' => 'secondary',
        'desc' => 'Digital sticky-notes board with color themes, category organization, note pinning for priority items, and instant live text search.',
        'model' => 'Note',
        'controller' => 'NoteController',
        'view' => 'note_view.php'
    ],
    [
        'id' => 7,
        'title' => 'Event Calendar',
        'folder' => '07-event-calendar',
        'icon' => 'bi-calendar-event',
        'color' => 'primary',
        'desc' => 'Interactive monthly calendar grid with complete event management (create, edit, delete), color tags, and automated notification reminders.',
        'model' => 'Event',
        'controller' => 'EventController',
        'view' => 'event_view.php'
    ],
    [
        'id' => 8,
        'title' => 'Recipe Platform',
        'folder' => '08-recipe-platform',
        'icon' => 'bi-egg-fried',
        'color' => 'success',
        'desc' => 'Culinary sharing platform to browse, filter by meal type or ingredients, view detailed step-by-step instructions, and publish new cooking recipes.',
        'model' => 'Recipe',
        'controller' => 'RecipeController',
        'view' => 'recipe_view.php'
    ],
    [
        'id' => 9,
        'title' => 'Memory Game',
        'folder' => '09-memory-game',
        'icon' => 'bi-grid-3x3-gap',
        'color' => 'purple',
        'desc' => 'Classic 3D card-matching memory game with 3 difficulty levels (Easy, Medium, Hard), live stopwatch timer, move counter, and persistent high scores leaderboard.',
        'model' => 'MemoryGame',
        'controller' => 'GameController',
        'view' => 'game_view.php'
    ],
    [
        'id' => 10,
        'title' => 'Poll Platform',
        'folder' => '10-poll-platform',
        'icon' => 'bi-bar-chart-line',
        'color' => 'warning',
        'desc' => 'Online survey and polling tool allowing users to create questions, vote with session protection, and view dynamic visual reports rendered with Chart.js.',
        'model' => 'Poll',
        'controller' => 'PollController',
        'view' => 'poll_view.php'
    ],
    [
        'id' => 11,
        'title' => 'Online Stopwatch',
        'folder' => '11-online-stopwatch',
        'icon' => 'bi-stopwatch',
        'color' => 'dark',
        'desc' => 'High-precision digital stopwatch with start, pause, resume, reset, lap recording with fastest/slowest lap detection, session saving, and CSV export.',
        'model' => 'Stopwatch',
        'controller' => 'StopwatchController',
        'view' => 'stopwatch_view.php'
    ]
];
?>

<div class="container py-3">
    <!-- Hero Banner -->
    <div class="card border-0 rounded-4 text-white p-4 p-md-5 mb-5 shadow-lg position-relative overflow-hidden" 
         style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-8">
                <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <i class="bi bi-mortarboard-fill me-1"></i> Web-Oriented Programming with PHP
                </span>
                <h1 class="display-5 fw-extrabold mb-3 text-white">
                    PHP Portfolio &bull; MVC Architecture
                </h1>
                <p class="lead text-white-50 mb-4" style="max-width: 650px;">
                    Comprehensive collection of <strong>11 full-stack web applications</strong> developed under the <strong>Model-View-Controller (MVC)</strong> pattern, featuring autonomous SQLite persistence and responsive Bootstrap design.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#exercises" class="btn btn-warning px-4 py-2 fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-grid-fill"></i> Explore All 11 Exercises
                    </a>
                    <a href="https://github.com/manolo700/Ejercicios-seguimiento-PHP" target="_blank" class="btn btn-outline-light px-4 py-2 fw-semibold d-flex align-items-center gap-2">
                        <i class="bi bi-github"></i> View on GitHub
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center d-none d-lg-block">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 backdrop-blur">
                    <i class="bi bi-cpu-fill text-warning display-2 mb-3 d-block"></i>
                    <h5 class="fw-bold mb-1">100% Functional</h5>
                    <p class="text-white-50 small mb-0">Autonomous SQLite storage with automatic table initialization and zero external setup needed.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Architecture Summary Card -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card app-card border-0 p-4 h-100">
                <div class="p-3 bg-primary-subtle text-primary rounded-3 d-inline-block mb-3" style="width: fit-content;">
                    <i class="bi bi-database-fill-gear fs-3"></i>
                </div>
                <h5 class="fw-bold">Model</h5>
                <p class="text-muted small mb-0">
                    Encapsulates business rules, data structures, and database persistence using prepared PDO SQLite statements and automatic schema creation.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card app-card border-0 p-4 h-100">
                <div class="p-3 bg-success-subtle text-success rounded-3 d-inline-block mb-3" style="width: fit-content;">
                    <i class="bi bi-window-sidebar fs-3"></i>
                </div>
                <h5 class="fw-bold">View</h5>
                <p class="text-muted small mb-0">
                    Renders modern, responsive user interfaces powered by Bootstrap 5, Bootstrap Icons, Chart.js statistics, and dynamic client-side interactions.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card app-card border-0 p-4 h-100">
                <div class="p-3 bg-warning-subtle text-warning-emphasis rounded-3 d-inline-block mb-3" style="width: fit-content;">
                    <i class="bi bi-diagram-3-fill fs-3"></i>
                </div>
                <h5 class="fw-bold">Controller</h5>
                <p class="text-muted small mb-0">
                    Processes incoming HTTP requests (GET/POST), validates input payloads, orchestrates model operations, and delivers data to views.
                </p>
            </div>
        </div>
    </div>

    <!-- Exercises Grid Section -->
    <div class="d-flex justify-content-between align-items-center mb-4" id="exercises">
        <div>
            <h3 class="fw-bold mb-1">Project Catalog</h3>
            <p class="text-muted small mb-0">Click "Open Application" on any module below to interact with it.</p>
        </div>
        <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">11 / 11 Completed</span>
    </div>

    <div class="row g-4 mb-5">
        <?php foreach ($exercisesList as $ex): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card app-card border-0 h-100 d-flex flex-column justify-content-between">
                    <div class="p-4 pb-2">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-monospace fw-bold">
                                Exercise #<?= sprintf('%02d', $ex['id']) ?>
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-check-circle-fill me-1"></i>Active MVC
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="p-3 bg-<?= $ex['color'] ?>-subtle text-<?= $ex['color'] ?> rounded-3">
                                <i class="bi <?= $ex['icon'] ?> fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-0"><?= htmlspecialchars($ex['title']) ?></h5>
                        </div>

                        <p class="text-muted small mb-3">
                            <?= htmlspecialchars($ex['desc']) ?>
                        </p>

                        <!-- MVC specs pill -->
                        <div class="p-2 bg-light rounded-3 small font-monospace" style="font-size: 0.72rem;">
                            <div class="text-muted"><strong>M:</strong> <?= $ex['model'] ?>.php</div>
                            <div class="text-muted"><strong>C:</strong> <?= $ex['controller'] ?>.php</div>
                            <div class="text-muted"><strong>V:</strong> <?= $ex['view'] ?></div>
                        </div>
                    </div>

                    <div class="p-4 pt-0">
                        <hr class="my-3 text-muted opacity-25">
                        <a href="<?= $ex['folder'] ?>/index.php" class="btn btn-brand w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                            <span>Open Application</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Instructions on how to run locally -->
    <div class="card app-card border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="card-title mb-0 fw-semibold">
                <i class="bi bi-terminal text-primary me-2"></i>Execution Instructions
            </h5>
        </div>
        <div class="card-body p-4 small">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="fw-bold"><i class="bi bi-laptop me-1 text-primary"></i> Option A: Apache via XAMPP</h6>
                    <ol class="ps-3 text-muted">
                        <li>Start the <strong>Apache</strong> module in the XAMPP Control Panel.</li>
                        <li>Ensure this project folder is inside your <code>htdocs</code> directory:
                            <br><code>c:\xampp\htdocs\SEGUIMIENTO PHP</code>
                        </li>
                        <li>Open your browser and visit:
                            <br><a href="http://localhost/SEGUIMIENTO%20PHP/" target="_blank" class="fw-bold">http://localhost/SEGUIMIENTO%20PHP/</a>
                        </li>
                    </ol>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold"><i class="bi bi-code-slash me-1 text-success"></i> Option B: PHP Built-in Server</h6>
                    <ol class="ps-3 text-muted">
                        <li>Open a terminal or command prompt in this root folder.</li>
                        <li>Run the following command:
                            <br><code class="d-inline-block p-1 bg-light border rounded">php -S localhost:8000</code>
                        </li>
                        <li>Open your browser and navigate to:
                            <br><a href="http://localhost:8000" target="_blank" class="fw-bold">http://localhost:8000</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/shared/footer.php'; ?>
