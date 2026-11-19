<?php
// Exercise 05: Front Controller
require_once __DIR__ . '/controllers/BookingController.php';

$controller = new BookingController();
$controller->handleRequest();

