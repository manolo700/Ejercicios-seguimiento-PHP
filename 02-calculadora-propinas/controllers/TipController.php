<?php
require_once __DIR__ . '/../models/TipCalculator.php';

class TipController {
    private TipCalculator $model;

    public function __construct() {
        $this->model = new TipCalculator();
    }

    public function handleRequest(): void {
        $action = $_GET['action'] ?? 'index';

        if ($action === 'clear_history') {
            $this->model->clearHistory();
            header('Location: index.php');
            exit;
        }

        $billAmount = isset($_POST['bill_amount']) ? (float)$_POST['bill_amount'] : 0.0;
        $tipPercent = isset($_POST['tip_percent']) ? (float)$_POST['tip_percent'] : 15.0;
        $numPeople = isset($_POST['num_people']) ? max(1, (int)$_POST['num_people']) : 1;
        $roundUp = isset($_POST['round_up']) && $_POST['round_up'] === '1';

        $calculated = false;
        $calculation = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $billAmount > 0) {
            $calculation = $this->model->calculate($billAmount, $tipPercent, $numPeople, $roundUp);
            $calculated = true;
        }

        $history = $this->model->getHistory();
        require __DIR__ . '/../views/tip_view.php';
    }
}

