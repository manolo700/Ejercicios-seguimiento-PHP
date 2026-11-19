<?php
// Exercise 03: Front Controller
require_once __DIR__ . '/controllers/PasswordController.php';

$controller = new PasswordController();
$controller->handleRequest();

