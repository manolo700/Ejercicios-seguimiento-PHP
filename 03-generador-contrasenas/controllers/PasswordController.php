<?php
require_once __DIR__ . '/../models/PasswordGenerator.php';

class PasswordController {
    private PasswordGenerator $model;

    public function __construct() {
        $this->model = new PasswordGenerator();
    }

    public function handleRequest(): void {
        $isPost = ($_SERVER['REQUEST_METHOD'] === 'POST');

        $length = isset($_REQUEST['length']) ? (int)$_REQUEST['length'] : 16;
        $quantity = isset($_REQUEST['quantity']) ? (int)$_REQUEST['quantity'] : 1;

        if ($isPost) {
            $useUpper = isset($_POST['upper']);
            $useLower = isset($_POST['lower']);
            $useNumbers = isset($_POST['numbers']);
            $useSymbols = isset($_POST['symbols']);
            $excludeAmbiguous = isset($_POST['exclude_ambiguous']);
        } else {
            // Default configuration
            $useUpper = true;
            $useLower = true;
            $useNumbers = true;
            $useSymbols = true;
            $excludeAmbiguous = false;
        }

        $result = $this->model->generate(
            $length,
            $useUpper,
            $useLower,
            $useNumbers,
            $useSymbols,
            $excludeAmbiguous,
            $quantity
        );

        require __DIR__ . '/../views/password_view.php';
    }
}

