<?php
// Exercise 04: Front Controller
require_once __DIR__ . '/controllers/ExpenseController.php';

$controller = new ExpenseController();
$controller->handleRequest();

