<?php
require_once __DIR__ . '/../models/Booking.php';

class BookingController {
    private Booking $model;

    public function __construct() {
        $this->model = new Booking();
    }

    public function handleRequest(): void {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            case 'book':
                $this->book();
                break;
            case 'cancel':
                $this->cancel();
                break;
            case 'index':
            default:
                $this->index();
                break;
        }
    }

    private function index(): void {
        $selectedDate = $_GET['date'] ?? date('Y-m-d', strtotime('+1 day'));
        $selectedService = $_GET['service'] ?? 'med';

        if (!array_key_exists($selectedService, Booking::SERVICES)) {
            $selectedService = 'med';
        }

        $services = Booking::SERVICES;
        $slots = $this->model->getAvailableSlots($selectedDate, $selectedService);
        $recentBookings = $this->model->getAll();

        $confirmationCode = $_GET['confirmed'] ?? null;
        $confirmedBooking = null;
        if ($confirmationCode) {
            $confirmedBooking = $this->model->getByCode($confirmationCode);
        }

        require __DIR__ . '/../views/booking_view.php';
    }

    private function book(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $date = $_POST['booking_date'] ?? date('Y-m-d');
            $time = $_POST['booking_time'] ?? '';
            $service = $_POST['service_id'] ?? 'med';
            $name = $_POST['client_name'] ?? '';
            $email = $_POST['client_email'] ?? '';
            $phone = $_POST['client_phone'] ?? '';
            $notes = $_POST['notes'] ?? '';

            if (!empty($name) && !empty($time) && !empty($date)) {
                $code = $this->model->create([
                    'client_name' => $name,
                    'client_email' => $email,
                    'client_phone' => $phone,
                    'service_id' => $service,
                    'booking_date' => $date,
                    'booking_time' => $time,
                    'notes' => $notes
                ]);
                header("Location: index.php?confirmed=" . urlencode($code));
                exit;
            }
        }
        header('Location: index.php');
        exit;
    }

    private function cancel(): void {
        $code = $_GET['code'] ?? '';
        if (!empty($code)) {
            $this->model->cancel($code);
        }
        header('Location: index.php');
        exit;
    }
}

