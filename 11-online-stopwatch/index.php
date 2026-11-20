<?php
// Exercise 11: Front Controller
require_once __DIR__ . '/controllers/StopwatchController.php';

$controller = new StopwatchController();
$controller->handleRequest();

