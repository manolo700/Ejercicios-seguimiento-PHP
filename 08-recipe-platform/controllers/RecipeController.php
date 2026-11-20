<?php
/**
 * Recipe Controller
 * Coordinates recipe browsing, searching, and submissions.
 */
require_once __DIR__ . '/../models/Recipe.php';

class RecipeController {
    private Recipe $model;

    public function __construct() {
        $this->model = new Recipe();
    }

    public function handleRequest(): void {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            case 'create':
                $this->create();
                break;
            case 'delete':
                $this->delete();
                break;
            case 'index':
            default:
                $this->index();
                break;
        }
    }

    private function index(): void {
        $search = $_GET['search'] ?? '';
        $mealType = $_GET['meal_type'] ?? '';
        $difficulty = $_GET['difficulty'] ?? '';

        $recipes = $this->model->getAll($search, $mealType, $difficulty);
        $mealTypes = Recipe::MEAL_TYPES;
        $difficulties = Recipe::DIFFICULTIES;

        require __DIR__ . '/../views/recipe_view.php';
    }

    private function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $ingredients = $_POST['ingredients'] ?? '';
            $instructions = $_POST['instructions'] ?? '';

            if (!empty($title) && !empty($ingredients) && !empty($instructions)) {
                $this->model->create([
                    'title' => $title,
                    'description' => $_POST['description'] ?? '',
                    'ingredients' => $ingredients,
                    'instructions' => $instructions,
                    'meal_type' => $_POST['meal_type'] ?? 'Lunch',
                    'difficulty' => $_POST['difficulty'] ?? 'Medium',
                    'prep_time_minutes' => (int)($_POST['prep_time_minutes'] ?? 30),
                    'servings' => (int)($_POST['servings'] ?? 4),
                    'image_url' => $_POST['image_url'] ?? '',
                    'author_name' => $_POST['author_name'] ?? 'Community Chef'
                ]);
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
