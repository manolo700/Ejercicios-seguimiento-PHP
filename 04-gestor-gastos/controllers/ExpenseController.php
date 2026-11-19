<?php
/**
 * Expense Controller
 * Handles monthly filtering, expense creation, and deletion requests.
 */
require_once __DIR__ . '/../models/Expense.php';

class ExpenseController {
    private Expense $model;

    public function __construct() {
        $this->model = new Expense();
    }

    public function handleRequest(): void {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            case 'add':
                $this->add();
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
        $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');

        if ($month < 1 || $month > 12) $month = (int)date('m');
        if ($year < 2000 || $year > 2100) $year = (int)date('Y');

        $expenses = $this->model->getByMonth($year, $month);
        $stats = $this->model->getMonthlyStats($year, $month);
        $categoryBreakdown = $this->model->getCategoryBreakdown($year, $month);
        $categories = Expense::CATEGORIES;

        require __DIR__ . '/../views/expense_view.php';
    }

    private function add(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0.0;
            $category = $_POST['category'] ?? 'Other';
            $date = $_POST['expense_date'] ?? date('Y-m-d');
            $notes = $_POST['notes'] ?? '';

            $this->model->add($title, $amount, $category, $date, $notes);

            $dateParts = explode('-', $date);
            $year = $dateParts[0] ?? date('Y');
            $month = $dateParts[1] ?? date('m');
            header("Location: index.php?year={$year}&month={$month}");
            exit;
        }
        header('Location: index.php');
        exit;
    }

    private function delete(): void {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? date('m');

        if ($id > 0) {
            $this->model->delete($id);
        }
        header("Location: index.php?year={$year}&month={$month}");
        exit;
    }
}
