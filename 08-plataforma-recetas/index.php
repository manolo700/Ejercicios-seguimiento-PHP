<?php
// Exercise 08: Front Controller
require_once __DIR__ . '/controllers/RecipeController.php';

$controller = new RecipeController();
$controller->handleRequest();
