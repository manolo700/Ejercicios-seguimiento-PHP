<?php
class Expense {
    private PDO $db;

    public const CATEGORIES = [
        'Comida' => ['icon' => 'bi-cup-hot', 'color' => 'danger'],
        'Transporte' => ['icon' => 'bi-car-front', 'color' => 'primary'],
        'Vivienda' => ['icon' => 'bi-house-door', 'color' => 'warning'],
        'Entretenimiento' => ['icon' => 'bi-controller', 'color' => 'purple'],
        'Salud' => ['icon' => 'bi-heart-pulse', 'color' => 'success'],
        'Educación' => ['icon' => 'bi-book', 'color' => 'info'],
        'Otros' => ['icon' => 'bi-tag', 'color' => 'secondary']
    ];

    public function __construct() {
        $dbPath = __DIR__ . '/../data/expenses.sqlite';
        $this->db = new PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->initTable();
    }

    private function initTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS expenses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            amount REAL NOT NULL,
            category TEXT NOT NULL,
            expense_date DATE NOT NULL,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);

        // Seed sample data if empty
        $count = (int)$this->db->query("SELECT COUNT(*) FROM expenses")->fetchColumn();
        if ($count === 0) {
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

            $samples = [
                ['Supermercado semanal', 65.50, 'Comida', $today, 'Frutas, verduras y lácteos'],
                ['Combustible vehículo', 35.00, 'Transporte', $yesterday, 'Gasolina tanque'],
                ['Pago Servicio de Luz', 42.80, 'Vivienda', $threeDaysAgo, 'Recibo mensual'],
                ['Cine y palomitas', 18.00, 'Entretenimiento', $yesterday, 'Fin de semana'],
                ['Farmacia y vitaminas', 22.50, 'Salud', $threeDaysAgo, 'Suplementos']
            ];

            $stmt = $this->db->prepare("INSERT INTO expenses (title, amount, category, expense_date, notes) VALUES (?, ?, ?, ?, ?)");
            foreach ($samples as $s) {
                $stmt->execute($s);
            }
        }
    }

    public function add(string $title, float $amount, string $category, string $date, string $notes = ''): bool {
        if (trim($title) === '' || $amount <= 0) return false;
        if (!array_key_exists($category, self::CATEGORIES)) {
            $category = 'Otros';
        }
        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $stmt = $this->db->prepare("INSERT INTO expenses (title, amount, category, expense_date, notes) VALUES (:title, :amount, :cat, :date, :notes)");
        return $stmt->execute([
            ':title' => trim($title),
            ':amount' => $amount,
            ':cat' => $category,
            ':date' => $date,
            ':notes' => trim($notes)
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM expenses WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getByMonth(int $year, int $month): array {
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = date('Y-m-t', strtotime($start));

        $stmt = $this->db->prepare("SELECT * FROM expenses WHERE expense_date BETWEEN :start AND :end ORDER BY expense_date DESC, id DESC");
        $stmt->execute([':start' => $start, ':end' => $end]);
        return $stmt->fetchAll();
    }

    public function getMonthlyStats(int $year, int $month): array {
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = date('Y-m-t', strtotime($start));
        $daysInMonth = (int)date('t', strtotime($start));

        $stmt = $this->db->prepare("SELECT 
            COUNT(*) as total_count,
            COALESCE(SUM(amount), 0) as total_amount,
            COALESCE(MAX(amount), 0) as max_amount,
            COALESCE(AVG(amount), 0) as avg_expense
            FROM expenses WHERE expense_date BETWEEN :start AND :end");
        $stmt->execute([':start' => $start, ':end' => $end]);
        $stats = $stmt->fetch();

        $stats['daily_average'] = $daysInMonth > 0 ? ($stats['total_amount'] / $daysInMonth) : 0;
        return $stats;
    }

    public function getCategoryBreakdown(int $year, int $month): array {
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = date('Y-m-t', strtotime($start));

        $stmt = $this->db->prepare("SELECT category, SUM(amount) as total, COUNT(*) as count 
            FROM expenses 
            WHERE expense_date BETWEEN :start AND :end 
            GROUP BY category 
            ORDER BY total DESC");
        $stmt->execute([':start' => $start, ':end' => $end]);
        return $stmt->fetchAll();
    }
}
