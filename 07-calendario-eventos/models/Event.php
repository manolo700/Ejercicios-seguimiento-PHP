<?php
class Event {
    private PDO $db;

    public function __construct() {
        $dbPath = __DIR__ . '/../data/events.sqlite';
        $this->db = new PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->initTable();
    }

    private function initTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS events (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            event_date DATE NOT NULL,
            start_time TEXT NOT NULL,
            end_time TEXT,
            category TEXT DEFAULT 'general',
            color TEXT DEFAULT '#4f46e5',
            has_reminder INTEGER DEFAULT 0,
            reminder_note TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);

        // Preload sample events if empty
        $count = (int)$this->db->query("SELECT COUNT(*) FROM events")->fetchColumn();
        if ($count === 0) {
            $today = date('Y-m-d');
            $tomorrow = date('Y-m-d', strtotime('+1 day'));
            $nextWeek = date('Y-m-d', strtotime('+5 days'));

            $this->create([
                'title' => 'Entrega de Proyecto Web PHP',
                'description' => 'Revisión final de código y documentación en GitHub.',
                'event_date' => $today,
                'start_time' => '18:00',
                'end_time' => '20:00',
                'category' => 'Académico',
                'color' => '#dc2626',
                'has_reminder' => 1,
                'reminder_note' => 'Subir repositorio con el comando git push origin main'
            ]);

            $this->create([
                'title' => 'Reunión de Equipo / Sprint',
                'description' => 'Planificación semanal y avances.',
                'event_date' => $tomorrow,
                'start_time' => '10:00',
                'end_time' => '11:30',
                'category' => 'Trabajo',
                'color' => '#2563eb',
                'has_reminder' => 1,
                'reminder_note' => 'Tener listos los diagramas de arquitectura MVC'
            ]);

            $this->create([
                'title' => 'Taller de Seguridad Web',
                'description' => 'Mejores prácticas en PHP 8.2 y validación de entradas.',
                'event_date' => $nextWeek,
                'start_time' => '15:00',
                'end_time' => '17:00',
                'category' => 'Conferencia',
                'color' => '#16a34a',
                'has_reminder' => 0,
                'reminder_note' => ''
            ]);
        }
    }

    public function getEventsByMonth(int $year, int $month): array {
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = date('Y-m-t', strtotime($start));

        $stmt = $this->db->prepare("SELECT * FROM events WHERE event_date BETWEEN :start AND :end ORDER BY event_date ASC, start_time ASC");
        $stmt->execute([':start' => $start, ':end' => $end]);
        $rows = $stmt->fetchAll();

        $byDay = [];
        foreach ($rows as $ev) {
            $day = (int)date('j', strtotime($ev['event_date']));
            $byDay[$day][] = $ev;
        }
        return $byDay;
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM events WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool {
        $stmt = $this->db->prepare("INSERT INTO events 
            (title, description, event_date, start_time, end_time, category, color, has_reminder, reminder_note)
            VALUES (:title, :description, :date, :start_time, :end_time, :category, :color, :has_reminder, :reminder_note)");
        return $stmt->execute([
            ':title' => trim($data['title']),
            ':description' => trim($data['description'] ?? ''),
            ':date' => $data['event_date'],
            ':start_time' => $data['start_time'] ?? '09:00',
            ':end_time' => $data['end_time'] ?? '',
            ':category' => $data['category'] ?? 'general',
            ':color' => $data['color'] ?? '#4f46e5',
            ':has_reminder' => !empty($data['has_reminder']) ? 1 : 0,
            ':reminder_note' => trim($data['reminder_note'] ?? '')
        ]);
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE events SET 
            title = :title,
            description = :description,
            event_date = :date,
            start_time = :start_time,
            end_time = :end_time,
            category = :category,
            color = :color,
            has_reminder = :has_reminder,
            reminder_note = :reminder_note
            WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':title' => trim($data['title']),
            ':description' => trim($data['description'] ?? ''),
            ':date' => $data['event_date'],
            ':start_time' => $data['start_time'],
            ':end_time' => $data['end_time'] ?? '',
            ':category' => $data['category'] ?? 'general',
            ':color' => $data['color'] ?? '#4f46e5',
            ':has_reminder' => !empty($data['has_reminder']) ? 1 : 0,
            ':reminder_note' => trim($data['reminder_note'] ?? '')
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM events WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getUpcomingReminders(): array {
        $today = date('Y-m-d');
        $future = date('Y-m-d', strtotime('+7 days'));

        $stmt = $this->db->prepare("SELECT * FROM events 
            WHERE event_date BETWEEN :today AND :future AND has_reminder = 1 
            ORDER BY event_date ASC, start_time ASC");
        $stmt->execute([':today' => $today, ':future' => $future]);
        return $stmt->fetchAll();
    }
}
