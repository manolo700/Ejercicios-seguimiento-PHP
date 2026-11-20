<?php
// Exercise 07: Front Controller
require_once __DIR__ . '/controllers/EventController.php';

$controller = new EventController();
$controller->handleRequest();

