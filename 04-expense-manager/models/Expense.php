<?php
/**
 * Expense Model
 * Manages daily expenses and monthly financial aggregations.
 */
class Expense {
    private PDO $db;

    public const CATEGORIES = [
        'Food' => ['icon' => 'bi-cup-hot', 'color' => 'danger', 'es' => 'Comida'],
        'Transportation' => ['icon' => 'bi-car-front', 'color' => 'primary', 'es' => 'Transporte'],
        'Housing' => ['icon' => 'bi-house-door', 'color' => 'warning', 'es' => 'Vivienda'],
        'Entertainment' => ['icon' => 'bi-controller', 'color' => 'purple', 'es' => 'Entretenimiento'],
        'Health' => ['icon' => 'bi-heart-pulse', 'color' => 'success', 'es' => 'Salud'],
        'Education' => ['icon' => 'bi-book', 'color' => 'info', 'es' => 'Educación'],
        'Other' => ['icon' => 'bi-tag', 'color' => 'secondary', 'es' => 'Otros']
    ];

    public function __construct() {
        $dbPath = __DIR__ . '/../data/expenses.sqlite';
        $this->db = new PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->initializeTable();
    }

    private function initializeTable(): void {
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

        // Preload sample expense records if database is empty
        $count = (int)$this->db->query("SELECT COUNT(*) FROM expenses")->fetchColumn();
        if ($count === 0) {
            $today = date('Y-m-d');
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            $threeDaysAgo = date('Y-m-d', strtotime('-3 days'));

            $samples = [
                ['Weekly Groceries', 65.50, 'Food', $today, 'Fruits, vegetables, and dairy'],
                ['Vehicle Fuel', 35.00, 'Transportation', $yesterday, 'Gasoline refill'],
                ['Electricity Utility Bill', 42.80, 'Housing', $threeDaysAgo, 'Monthly electricity bill'],
                ['Cinema and Snacks', 18.00, 'Entertainment', $yesterday, 'Weekend movie'],
                ['Pharmacy & Vitamins', 22.50, 'Health', $threeDaysAgo, 'Daily supplements']
            ];

            $stmt = $this->db->prepare("INSERT INTO expenses (title, amount, category, expense_date, notes) VALUES (?, ?, ?, ?, ?)");
            foreach ($samples as $sample) {
                $stmt->execute($sample);
            }
        }
    }

    /**
     * Add a new expense.
     */
    public function add(string $title, float $amount, string $category, string $date, string $notes = ''): bool {
        $title = trim($title);
        if ($title === '' || $amount <= 0) {
            return false;
        }

        if (!array_key_exists($category, self::CATEGORIES)) {
            // Map Spanish legacy category if needed
            $matched = 'Other';
            foreach (self::CATEGORIES as $key => $meta) {
                if ($category === $meta['es'] || strtolower($category) === strtolower($key)) {
                    $matched = $key;
                    break;
                }
            }
            $category = $matched;
        }

        if (empty($date)) {
            $date = date('Y-m-d');
        }

        $stmt = $this->db->prepare("INSERT INTO expenses (title, amount, category, expense_date, notes) VALUES (:title, :amount, :cat, :date, :notes)");
        return $stmt->execute([
            ':title' => $title,
            ':amount' => $amount,
            ':cat' => $category,
            ':date' => $date,
            ':notes' => trim($notes)
        ]);
    }

    /**
     * Delete an expense by ID.
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM expenses WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Get all expenses for a specific month and year.
     */
    public function getByMonth(int $year, int $month): array {
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end = date('Y-m-t', strtotime($start));

        $stmt = $this->db->prepare("SELECT * FROM expenses WHERE expense_date BETWEEN :start AND :end ORDER BY expense_date DESC, id DESC");
        $stmt->execute([':start' => $start, ':end' => $end]);
        return $stmt->fetchAll();
    }

    /**
     * Get monthly metrics: total, count, max amount, and daily average.
     */
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

    /**
     * Get breakdown totals per category for a given month.
     */
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
