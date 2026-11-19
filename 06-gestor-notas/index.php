<?php
// Exercise 06: Front Controller
require_once __DIR__ . '/controllers/NoteController.php';

$controller = new NoteController();
$controller->handleRequest();

