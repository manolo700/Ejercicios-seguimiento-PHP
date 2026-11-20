<?php
// Exercise 02: Front Controller
require_once __DIR__ . '/controllers/TipController.php';

$controller = new TipController();
$controller->handleRequest();

