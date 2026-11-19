<?php
/**
 * Booking Model
 * Manages appointment services, real-time slot availability, and booking records.
 */
class Booking {
    private PDO $db;

    public const SERVICES = [
        'med' => ['name' => 'General Medical Consultation', 'duration' => '45 min', 'price' => 50.00, 'icon' => 'bi-heart-pulse'],
        'legal' => ['name' => 'Legal Advisory Session', 'duration' => '60 min', 'price' => 75.00, 'icon' => 'bi-briefcase'],
        'fisio' => ['name' => 'Physical Therapy Session', 'duration' => '50 min', 'price' => 40.00, 'icon' => 'bi-person-walking'],
        'tech' => ['name' => 'IT Support & Technical Service', 'duration' => '60 min', 'price' => 35.00, 'icon' => 'bi-laptop'],
        'fin' => ['name' => 'Financial & Accounting Advisory', 'duration' => '45 min', 'price' => 60.00, 'icon' => 'bi-currency-dollar']
    ];

    public const SLOTS = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'];

    public function __construct() {
        $dbPath = __DIR__ . '/../data/bookings.sqlite';
        $this->db = new PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->initializeTable();
    }

    private function initializeTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            booking_code TEXT UNIQUE NOT NULL,
            client_name TEXT NOT NULL,
            client_email TEXT NOT NULL,
            client_phone TEXT NOT NULL,
            service_id TEXT NOT NULL,
            booking_date DATE NOT NULL,
            booking_time TEXT NOT NULL,
            status TEXT DEFAULT 'CONFIRMED',
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);

        // Preload sample reservations for testing
        $count = (int)$this->db->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
        if ($count === 0) {
            $tomorrow = date('Y-m-d', strtotime('+1 day'));
            $this->create([
                'client_name' => 'Carlos Mendoza',
                'client_email' => 'carlos@example.com',
                'client_phone' => '555-0192',
                'service_id' => 'med',
                'booking_date' => $tomorrow,
                'booking_time' => '10:00',
                'notes' => 'Routine preventive checkup'
            ]);
            $this->create([
                'client_name' => 'Laura Gómez',
                'client_email' => 'laura@example.com',
                'client_phone' => '555-0847',
                'service_id' => 'legal',
                'booking_date' => $tomorrow,
                'booking_time' => '14:00',
                'notes' => 'Commercial contract review'
            ]);
        }
    }

    /**
     * Check available time slots for a given date and service.
     */
    public function getAvailableSlots(string $date, string $serviceId): array {
        $stmt = $this->db->prepare("SELECT booking_time FROM bookings 
            WHERE booking_date = :date AND status != 'CANCELLED' AND status != 'CANCELADA'");
        $stmt->execute([':date' => $date]);
        $bookedTimes = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $slots = [];
        foreach (self::SLOTS as $time) {
            $slots[] = [
                'time' => $time,
                'available' => !in_array($time, $bookedTimes)
            ];
        }
        return $slots;
    }

    /**
     * Create a new booking and return unique voucher reference code.
     */
    public function create(array $data): string {
        $code = 'RES-' . strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 6));

        $stmt = $this->db->prepare("INSERT INTO bookings 
            (booking_code, client_name, client_email, client_phone, service_id, booking_date, booking_time, notes)
            VALUES (:code, :name, :email, :phone, :service, :date, :time, :notes)");
        
        $stmt->execute([
            ':code' => $code,
            ':name' => trim($data['client_name']),
            ':email' => trim($data['client_email']),
            ':phone' => trim($data['client_phone']),
            ':service' => $data['service_id'],
            ':date' => $data['booking_date'],
            ':time' => $data['booking_time'],
            ':notes' => trim($data['notes'] ?? '')
        ]);

        return $code;
    }

    /**
     * Cancel an existing booking by its code.
     */
    public function cancel(string $code): bool {
        $stmt = $this->db->prepare("UPDATE bookings SET status = 'CANCELLED' WHERE booking_code = :code");
        return $stmt->execute([':code' => $code]);
    }

    /**
     * Retrieve all bookings ordered by appointment date.
     */
    public function getAll(int $limit = 10): array {
        $stmt = $this->db->prepare("SELECT * FROM bookings ORDER BY booking_date DESC, booking_time ASC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get booking details by voucher reference code.
     */
    public function getByCode(string $code): ?array {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE booking_code = :code");
        $stmt->execute([':code' => $code]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
