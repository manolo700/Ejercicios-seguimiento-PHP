<?php
require_once __DIR__ . '/../models/Event.php';

class EventController {
    private Event $model;

    public function __construct() {
        $this->model = new Event();
    }

    public function handleRequest(): void {
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            case 'save':
                $this->save();
                break;
            case 'delete':
                $this->delete();
                break;
            case 'index':
            default:
                $this->index();
                break;
        }
    }

    private function index(): void {
        $year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        $month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');

        if ($month < 1) {
            $month = 12;
            $year--;
        } elseif ($month > 12) {
            $month = 1;
            $year++;
        }

        $eventsByDay = $this->model->getEventsByMonth($year, $month);
        $reminders = $this->model->getUpcomingReminders();

        // Calendar mathematics
        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
        $daysInMonth = (int)date('t', $firstDayOfMonth);
        // Day of week (1 for Monday through 7 for Sunday)
        $firstDayOfWeek = (int)date('N', $firstDayOfMonth);

        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }

        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }

        require __DIR__ . '/../views/event_view.php';
    }

    private function save(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $date = $_POST['event_date'] ?? date('Y-m-d');
            $data = [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'event_date' => $date,
                'start_time' => $_POST['start_time'] ?? '09:00',
                'end_time' => $_POST['end_time'] ?? '',
                'category' => $_POST['category'] ?? 'General',
                'color' => $_POST['color'] ?? '#4f46e5',
                'has_reminder' => isset($_POST['has_reminder']) ? 1 : 0,
                'reminder_note' => $_POST['reminder_note'] ?? ''
            ];

            if ($id > 0) {
                $this->model->update($id, $data);
            } else {
                $this->model->create($data);
            }

            $dateParts = explode('-', $date);
            $y = $dateParts[0] ?? date('Y');
            $m = $dateParts[1] ?? date('m');
            header("Location: index.php?year={$y}&month={$m}");
            exit;
        }
        header('Location: index.php');
        exit;
    }

    private function delete(): void {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $year = $_GET['year'] ?? date('Y');
        $month = $_GET['month'] ?? date('m');

        if ($id > 0) {
            $this->model->delete($id);
        }
        header("Location: index.php?year={$year}&month={$month}");
        exit;
    }
}
