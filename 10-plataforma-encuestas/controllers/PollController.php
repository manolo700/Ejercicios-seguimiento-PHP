<?php
require_once __DIR__ . '/../models/Poll.php';

class PollController {
    private Poll $model;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['voter_token'])) {
            $_SESSION['voter_token'] = bin2hex(random_bytes(16));
        }
        $this->model = new Poll();
    }

    public function handleRequest(): void {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            case 'vote':
                $this->vote();
                break;
            case 'create':
                $this->create();
                break;
            case 'delete':
                $this->delete();
                break;
            case 'view':
            case 'index':
            default:
                $this->index();
                break;
        }
    }

    private function index(): void {
        $polls = $this->model->getAll();
        $selectedPollId = isset($_GET['poll_id']) ? (int)$_GET['poll_id'] : ($polls[0]['id'] ?? 0);
        $activePoll = null;

        if ($selectedPollId > 0) {
            $activePoll = $this->model->getById($selectedPollId);
        }

        $sessionToken = $_SESSION['voter_token'];
        $hasVoted = $activePoll ? $this->model->hasVoted($activePoll['id'], $sessionToken) : false;

        require __DIR__ . '/../views/poll_view.php';
    }

    private function vote(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pollId = (int)($_POST['poll_id'] ?? 0);
            $optionId = (int)($_POST['option_id'] ?? 0);
            $sessionToken = $_SESSION['voter_token'];

            if ($pollId > 0 && $optionId > 0) {
                $this->model->vote($pollId, $optionId, $sessionToken);
            }
            header("Location: index.php?poll_id={$pollId}");
            exit;
        }
        header('Location: index.php');
        exit;
    }

    private function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $desc = $_POST['description'] ?? '';
            $cat = $_POST['category'] ?? 'Tecnología';
            $options = $_POST['options'] ?? [];

            if (!empty($title) && is_array($options) && count($options) >= 2) {
                $pollId = $this->model->create($title, $desc, $cat, $options);
                header("Location: index.php?poll_id={$pollId}");
                exit;
            }
        }
        header('Location: index.php');
        exit;
    }

    private function delete(): void {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $this->model->delete($id);
        }
        header('Location: index.php');
        exit;
    }
}
