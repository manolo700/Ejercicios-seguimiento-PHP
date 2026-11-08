<?php
require_once __DIR__ . '/../models/MemoryGame.php';

class GameController {
    private MemoryGame $model;

    public function __construct() {
        $this->model = new MemoryGame();
    }

    public function handleRequest(): void {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            case 'save_score':
                $this->saveScore();
                break;
            case 'index':
            default:
                $this->index();
                break;
        }
    }

    private function index(): void {
        $difficulty = $_GET['difficulty'] ?? 'medio';
        if (!array_key_exists($difficulty, MemoryGame::LEVELS)) {
            $difficulty = 'medio';
        }

        $cards = $this->model->generateCards($difficulty);
        $levelInfo = MemoryGame::LEVELS[$difficulty];
        $leaderboard = $this->model->getLeaderboard($difficulty);
        $allLevels = MemoryGame::LEVELS;

        require __DIR__ . '/../views/game_view.php';
    }

    private function saveScore(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['player_name'] ?? 'Jugador';
            $diff = $_POST['difficulty'] ?? 'medio';
            $moves = (int)($_POST['moves'] ?? 0);
            $time = (int)($_POST['time_seconds'] ?? 0);
            $score = (int)($_POST['score'] ?? 0);

            $this->model->saveScore($name, $diff, $moves, $time, $score);

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                exit;
            }

            header("Location: index.php?difficulty=" . urlencode($diff));
            exit;
        }
    }
}
