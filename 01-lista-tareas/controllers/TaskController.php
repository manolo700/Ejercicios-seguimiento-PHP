<?php
require_once __DIR__ . '/../models/Task.php';

class TaskController {
    private Task $model;

    public function __construct() {
        $this->model = new Task();
    }

    public function handleRequest(): void {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            case 'add':
                $this->add();
                break;
            case 'toggle':
                $this->toggle();
                break;
            case 'delete':
                $this->delete();
                break;
            case 'clear_completed':
                $this->clearCompleted();
                break;
            case 'index':
            default:
                $this->index();
                break;
        }
    }

    private function index(): void {
        $filter = $_GET['filter'] ?? 'all';
        $tasks = $this->model->getAll($filter);
        $stats = $this->model->getStats();
        require __DIR__ . '/../views/task_view.php';
    }

    private function add(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $priority = $_POST['priority'] ?? 'media';
            $this->model->add($title, $priority);
        }
        $filter = $_POST['current_filter'] ?? 'all';
        header('Location: index.php?filter=' . urlencode($filter));
        exit;
    }

    private function toggle(): void {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $this->model->toggle($id);
        }
        $filter = $_GET['filter'] ?? 'all';
        header('Location: index.php?filter=' . urlencode($filter));
        exit;
    }

    private function delete(): void {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $this->model->delete($id);
        }
        $filter = $_GET['filter'] ?? 'all';
        header('Location: index.php?filter=' . urlencode($filter));
        exit;
    }

    private function clearCompleted(): void {
        $this->model->clearCompleted();
        $filter = $_GET['filter'] ?? 'all';
        header('Location: index.php?filter=' . urlencode($filter));
        exit;
    }
}
