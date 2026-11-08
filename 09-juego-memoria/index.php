<?php
// Exercise 09: Front Controller
require_once __DIR__ . '/controllers/GameController.php';

$controller = new GameController();
$controller->handleRequest();
