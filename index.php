<?php
$pageTitle = 'Portafolio de Ejercicios PHP MVC';
$activeExercise = 0;
$depth = 0;
require __DIR__ . '/shared/header.php';

$exercisesList = [
    [
        'id' => 1,
        'title' => 'Lista de Tareas',
        'folder' => '01-lista-tareas',
        'icon' => 'bi-check2-square',
        'color' => 'primary',
        'desc' => 'Aplicación interactiva que permite a los usuarios agregar, eliminar y marcar tareas completadas con filtrado y persistencia en base de datos SQLite.',
        'model' => 'Task',
        'controller' => 'TaskController',
        'view' => 'task_view.php'
    ],
    [
        'id' => 2,
        'title' => 'Calculadora de Propinas',
        'folder' => '02-calculadora-propinas',
        'icon' => 'bi-calculator',
        'color' => 'success',
        'desc' => 'Calcula automáticamente el monto de propina, total con propina, división equitativa entre comensales y registro histórico de cuentas.',
        'model' => 'TipCalculator',
        'controller' => 'TipController',
        'view' => 'tip_view.php'
    ],
    [
        'id' => 3,
        'title' => 'Generador de Contraseñas',
        'folder' => '03-generador-contrasenas',
        'icon' => 'bi-shield-lock',
        'color' => 'warning',
        'desc' => 'Generador de contraseñas de alta entropía con longitud configurable, control de tipos de caracteres, medidor de seguridad y copia rápida.',
        'model' => 'PasswordGenerator',
        'controller' => 'PasswordController',
        'view' => 'password_view.php'
    ],
    [
        'id' => 4,
        'title' => 'Gestor de Gastos',
        'folder' => '04-gestor-gastos',
        'icon' => 'bi-cash-coin',
        'color' => 'danger',
        'desc' => 'Registro de gastos diarios con categorización, desglose porcentual mensual, promedios diarios e indicadores financieros en tiempo real.',
        'model' => 'Expense',
        'controller' => 'ExpenseController',
        'view' => 'expense_view.php'
    ],
    [
        'id' => 5,
        'title' => 'Sistema de Reservas',
        'folder' => '05-sistema-reservas',
        'icon' => 'bi-calendar-check',
        'color' => 'info',
        'desc' => 'Plataforma para reservar servicios o citas en línea con verificación de disponibilidad de horarios en tiempo real y emisión de ticket de confirmación.',
        'model' => 'Booking',
        'controller' => 'BookingController',
        'view' => 'booking_view.php'
    ],
    [
        'id' => 6,
        'title' => 'Gestor de Notas',
        'folder' => '06-gestor-notas',
        'icon' => 'bi-journal-text',
        'color' => 'secondary',
        'desc' => 'Organizador de notas estilo post-it con clasificación por categorías temáticas, fijación prioritaria, paleta de colores y buscador instantáneo.',
        'model' => 'Note',
        'controller' => 'NoteController',
        'view' => 'note_view.php'
    ],
    [
        'id' => 7,
        'title' => 'Calendario de Eventos',
        'folder' => '07-calendario-eventos',
        'icon' => 'bi-calendar-event',
        'color' => 'primary',
        'desc' => 'Calendario interactivo mensual con cuadrícula de días, CRUD completo de eventos, recordatorios y notificaciones automáticas para los próximos 7 días.',
        'model' => 'Event',
        'controller' => 'EventController',
        'view' => 'event_view.php'
    ],
    [
        'id' => 8,
        'title' => 'Plataforma de Recetas',
        'folder' => '08-plataforma-recetas',
        'icon' => 'bi-egg-fried',
        'color' => 'success',
        'desc' => 'Catálogo culinario con buscador, filtros por tipo de comida o ingredientes, fichas detalladas de preparación y publicación de nuevas recetas.',
        'model' => 'Recipe',
        'controller' => 'RecipeController',
        'view' => 'recipe_view.php'
    ],
    [
        'id' => 9,
        'title' => 'Juego de Memoria',
        'folder' => '09-juego-memoria',
        'icon' => 'bi-grid-3x3-gap',
        'color' => 'purple',
        'desc' => 'Juego interactivo de emparejar cartas con animación 3D de volteo, 3 niveles de dificultad, cronómetro, contador de movimientos y tabla de mejores puntajes.',
        'model' => 'MemoryGame',
        'controller' => 'GameController',
        'view' => 'game_view.php'
    ],
    [
        'id' => 10,
        'title' => 'Plataforma de Encuestas',
        'folder' => '10-plataforma-encuestas',
        'icon' => 'bi-bar-chart-line',
        'color' => 'warning',
        'desc' => 'Sistema para crear y responder encuestas con control de votación por sesión, cálculo de porcentajes y generación de informes visuales con Chart.js.',
        'model' => 'Poll',
        'controller' => 'PollController',
        'view' => 'poll_view.php'
    ],
    [
        'id' => 11,
        'title' => 'Cronómetro Online',
        'folder' => '11-cronometro-online',
        'icon' => 'bi-stopwatch',
        'color' => 'dark',
        'desc' => 'Cronómetro digital con inicio, pausa, reinicio, captura de vueltas (tiempos parciales), distinción de vuelta más rápida/lenta y exportación a CSV.',
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
                    <i class="bi bi-mortarboard-fill me-1"></i> Programación Orientada a la Web con PHP
                </span>
                <h1 class="display-5 fw-extrabold mb-3 text-white">
                    Seguimiento PHP &bull; Arquitectura MVC
                </h1>
                <p class="lead text-white-50 mb-4" style="max-width: 650px;">
                    Colección integral de 11 ejercicios prácticos desarrollados bajo el patrón de diseño <strong>Modelo-Vista-Controlador (MVC)</strong>, con persistencia autónoma en SQLite y diseño web moderno y responsivo.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#ejercicios" class="btn btn-warning px-4 py-2 fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-grid-fill"></i> Explorar los 11 Ejercicios
                    </a>
                    <a href="https://github.com/manolo700/Ejercicios-seguimiento-PHP" target="_blank" class="btn btn-outline-light px-4 py-2 fw-semibold d-flex align-items-center gap-2">
                        <i class="bi bi-github"></i> Ver en GitHub
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center d-none d-lg-block">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 backdrop-blur">
                    <i class="bi bi-cpu-fill text-warning display-2 mb-3 d-block"></i>
                    <h5 class="fw-bold mb-1">100% Funcional</h5>
                    <p class="text-white-50 small mb-0">Base de datos SQLite integrada, sin necesidad de configuración externa.</p>
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
                <h5 class="fw-bold">Modelo (Model)</h5>
                <p class="text-muted small mb-0">
                    Encapsula la lógica de negocio, reglas de validación y acceso a datos mediante PDO SQLite con inicialización automática de esquemas.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card app-card border-0 p-4 h-100">
                <div class="p-3 bg-success-subtle text-success rounded-3 d-inline-block mb-3" style="width: fit-content;">
                    <i class="bi bi-window-sidebar fs-3"></i>
                </div>
                <h5 class="fw-bold">Vista (View)</h5>
                <p class="text-muted small mb-0">
                    Muestra interfaces de usuario intuitivas con Bootstrap 5, Bootstrap Icons, gráficos con Chart.js e interacciones dinámicas.
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card app-card border-0 p-4 h-100">
                <div class="p-3 bg-warning-subtle text-warning-emphasis rounded-3 d-inline-block mb-3" style="width: fit-content;">
                    <i class="bi bi-diagram-3-fill fs-3"></i>
                </div>
                <h5 class="fw-bold">Controlador (Controller)</h5>
                <p class="text-muted small mb-0">
                    Procesa las peticiones HTTP (GET/POST), coordina los modelos y pasa la información necesaria a las vistas correspondientes.
                </p>
            </div>
        </div>
    </div>

    <!-- Exercises Grid Section -->
    <div class="d-flex justify-content-between align-items-center mb-4" id="ejercicios">
        <div>
            <h3 class="fw-bold mb-1">Catálogo de Ejercicios Realizados</h3>
            <p class="text-muted small mb-0">Haz clic en "Abrir Aplicación" en cualquiera de los módulos para interactuar con él.</p>
        </div>
        <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">11 / 11 Completados</span>
    </div>

    <div class="row g-4 mb-5">
        <?php foreach ($exercisesList as $ex): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card app-card border-0 h-100 d-flex flex-column justify-content-between">
                    <div class="p-4 pb-2">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill font-monospace fw-bold">
                                Ejercicio #<?= sprintf('%02d', $ex['id']) ?>
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                <i class="bi bi-check-circle-fill me-1"></i>MVC Activo
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
                            <span>Abrir Aplicación</span>
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
                <i class="bi bi-terminal text-primary me-2"></i>Instrucciones de Ejecución
            </h5>
        </div>
        <div class="card-body p-4 small">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="fw-bold"><i class="bi bi-laptop me-1 text-primary"></i> Opción A: Servidor Apache en XAMPP</h6>
                    <ol class="ps-3 text-muted">
                        <li>Inicia el módulo <strong>Apache</strong> en el panel de control de XAMPP.</li>
                        <li>Asegúrate de que la carpeta esté ubicada dentro de <code>htdocs</code>:
                            <br><code>c:\xampp\htdocs\SEGUIMIENTO PHP</code>
                        </li>
                        <li>Abre tu navegador web y visita:
                            <br><a href="http://localhost/SEGUIMIENTO%20PHP/" target="_blank" class="fw-bold">http://localhost/SEGUIMIENTO%20PHP/</a>
                        </li>
                    </ol>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold"><i class="bi bi-code-slash me-1 text-success"></i> Opción B: Servidor Integrado de PHP</h6>
                    <ol class="ps-3 text-muted">
                        <li>Abre una terminal o consola de comandos en este directorio.</li>
                        <li>Ejecuta el siguiente comando:
                            <br><code class="d-inline-block p-1 bg-light border rounded">php -S localhost:8000</code>
                        </li>
                        <li>Abre tu navegador web y visita:
                            <br><a href="http://localhost:8000" target="_blank" class="fw-bold">http://localhost:8000</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/shared/footer.php'; ?>
