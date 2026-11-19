<?php
require_once __DIR__ . '/../models/Stopwatch.php';

class StopwatchController {
    private Stopwatch $model;

    public function __construct() {
        $this->model = new Stopwatch();
    }

    public function handleRequest(): void {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            case 'save_session':
                $this->saveSession();
                break;
            case 'clear_history':
                $this->clearHistory();
                break;
            case 'index':
            default:
                $this->index();
                break;
        }
    }

    private function index(): void {
        $sessions = $this->model->getPastSessions();
        require __DIR__ . '/../views/stopwatch_view.php';
    }

    private function saveSession(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['session_name'] ?? 'Sesión Cronómetro';
            $totalTime = (int)($_POST['total_time_ms'] ?? 0);
            $rawLaps = $_POST['laps_json'] ?? '[]';
            $laps = json_decode($rawLaps, true) ?: [];

            $this->model->saveSession($name, $totalTime, $laps);
        }
        header('Location: index.php');
        exit;
    }

    private function clearHistory(): void {
        $this->model->clearHistory();
        header('Location: index.php');
        exit;
    }
}

