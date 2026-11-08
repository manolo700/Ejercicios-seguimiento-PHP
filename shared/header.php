<?php
// Shared Header for all MVC exercises
if (!isset($pageTitle)) {
    $pageTitle = 'Ejercicio PHP';
}
if (!isset($activeExercise)) {
    $activeExercise = 0;
}
$basePath = isset($depth) && $depth === 1 ? '../' : './';

$exercises = [
    1 => ['name' => '1. Lista de Tareas', 'folder' => '01-lista-tareas', 'icon' => 'bi-check2-square'],
    2 => ['name' => '2. Calculadora de Propinas', 'folder' => '02-calculadora-propinas', 'icon' => 'bi-calculator'],
    3 => ['name' => '3. Generador de Contraseñas', 'folder' => '03-generador-contrasenas', 'icon' => 'bi-shield-lock'],
    4 => ['name' => '4. Gestor de Gastos', 'folder' => '04-gestor-gastos', 'icon' => 'bi-cash-coin'],
    5 => ['name' => '5. Sistema de Reservas', 'folder' => '05-sistema-reservas', 'icon' => 'bi-calendar-check'],
    6 => ['name' => '6. Gestor de Notas', 'folder' => '06-gestor-notas', 'icon' => 'bi-journal-text'],
    7 => ['name' => '7. Calendario de Eventos', 'folder' => '07-calendario-eventos', 'icon' => 'bi-calendar-event'],
    8 => ['name' => '8. Plataforma de Recetas', 'folder' => '08-plataforma-recetas', 'icon' => 'bi-egg-fried'],
    9 => ['name' => '9. Juego de Memoria', 'folder' => '09-juego-memoria', 'icon' => 'bi-grid-3x3-gap'],
    10 => ['name' => '10. Plataforma de Encuestas', 'folder' => '10-plataforma-encuestas', 'icon' => 'bi-bar-chart-line'],
    11 => ['name' => '11. Cronómetro Online', 'folder' => '11-cronometro-online', 'icon' => 'bi-stopwatch'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Arquitectura MVC</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --app-font: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --brand-primary: #4f46e5;
            --brand-primary-hover: #4338ca;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        }
        body {
            font-family: var(--app-font);
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-navbar {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }
        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .brand-badge {
            font-size: 0.72rem;
            letter-spacing: 0.5px;
            background: rgba(255,255,255,0.18);
            border: 1px solid rgba(255,255,255,0.25);
            padding: 4px 8px;
            border-radius: 9999px;
            text-transform: uppercase;
        }
        .nav-link {
            font-weight: 500;
        }
        .app-card {
            background: #ffffff;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            box-shadow: var(--card-shadow);
            transition: all 0.25s ease;
        }
        .app-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .mvc-badge {
            background-color: #e0e7ff;
            color: #3730a3;
            font-weight: 600;
            font-size: 0.75rem;
            border-radius: 6px;
            padding: 4px 8px;
        }
        .btn-brand {
            background-color: var(--brand-primary);
            color: white;
            font-weight: 600;
            border: none;
        }
        .btn-brand:hover {
            background-color: var(--brand-primary-hover);
            color: white;
        }
        .content-wrapper {
            flex: 1;
        }
        footer {
            margin-top: auto;
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark main-navbar py-2 sticky-top">
    <div class="container-fluid px-lg-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $basePath ?>index.php">
            <span class="p-2 rounded-3 bg-white text-primary d-inline-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                <i class="bi bi-layers-fill fs-5"></i>
            </span>
            <span>Seguimiento PHP</span>
            <span class="brand-badge ms-1">MVC Framework</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                <li class="nav-item">
                    <a class="nav-link <?= $activeExercise === 0 ? 'active fw-bold text-white' : '' ?>" href="<?= $basePath ?>index.php">
                        <i class="bi bi-house-door me-1"></i> Inicio / Todos
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="dropdownEjercicios" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-grid me-1"></i> Ejercicios (11)
                    </a>
                    <ul class="dropdown-menu shadow-lg border-0 py-2" style="max-height: 450px; overflow-y: auto;">
                        <?php foreach ($exercises as $num => $ex): ?>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 <?= $activeExercise === $num ? 'active fw-bold' : '' ?>" href="<?= $basePath . $ex['folder'] ?>/index.php">
                                    <i class="bi <?= $ex['icon'] ?> text-muted"></i>
                                    <span><?= htmlspecialchars($ex['name']) ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <a href="https://github.com/manolo700/Ejercicios-seguimiento-PHP" target="_blank" class="btn btn-sm btn-outline-light d-flex align-items-center gap-2">
                    <i class="bi bi-github"></i>
                    <span>Repositorio GitHub</span>
                </a>
            </div>
        </div>
    </div>
</nav>
<div class="content-wrapper py-4">
