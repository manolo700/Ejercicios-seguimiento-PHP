<?php
require_once __DIR__ . '/../models/Note.php';

class NoteController {
    private Note $model;

    public function __construct() {
        $this->model = new Note();
    }

    public function handleRequest(): void {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            case 'save':
                $this->save();
                break;
            case 'delete':
                $this->delete();
                break;
            case 'toggle_pin':
                $this->togglePin();
                break;
            case 'index':
            default:
                $this->index();
                break;
        }
    }

    private function index(): void {
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';

        $notes = $this->model->getAll($search, $category);
        $categoryStats = $this->model->getCategoryStats();
        $categories = Note::CATEGORIES;
        $colors = Note::COLORS;

        $editNote = null;
        if (isset($_GET['edit_id'])) {
            $editNote = $this->model->getById((int)$_GET['edit_id']);
        }

        require __DIR__ . '/../views/note_view.php';
    }

    private function save(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $category = $_POST['category'] ?? 'Personal';
            $color = $_POST['color'] ?? 'yellow';
            $pinned = isset($_POST['is_pinned']);

            if ($id > 0) {
                $this->model->update($id, $title, $content, $category, $color, $pinned);
            } else {
                $this->model->create($title, $content, $category, $color, $pinned);
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

    private function togglePin(): void {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $this->model->togglePin($id);
        }
        header('Location: index.php');
        exit;
    }
}
