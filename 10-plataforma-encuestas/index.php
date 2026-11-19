<?php
// Exercise 10: Front Controller
require_once __DIR__ . '/controllers/PollController.php';

$controller = new PollController();
$controller->handleRequest();

