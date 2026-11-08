<?php
// Exercise 01: Front Controller
require_once __DIR__ . '/controllers/TaskController.php';

$controller = new TaskController();
$controller->handleRequest();
