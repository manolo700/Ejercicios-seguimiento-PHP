<?php
class TipCalculator {
    private PDO $db;

    public function __construct() {
        $dbPath = __DIR__ . '/../data/tips.sqlite';
        $this->db = new PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->initTable();
    }

    private function initTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS tip_history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            bill_amount REAL NOT NULL,
            tip_percent REAL NOT NULL,
            tip_amount REAL NOT NULL,
            total_amount REAL NOT NULL,
            num_people INTEGER DEFAULT 1,
            total_per_person REAL NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);
    }

    public function calculate(float $billAmount, float $tipPercent, int $numPeople = 1, bool $roundUp = false): array {
        if ($billAmount < 0) $billAmount = 0;
        if ($tipPercent < 0) $tipPercent = 0;
        if ($numPeople < 1) $numPeople = 1;

        $tipAmount = $billAmount * ($tipPercent / 100);
        $totalAmount = $billAmount + $tipAmount;

        if ($roundUp) {
            $totalAmount = ceil($totalAmount);
            $tipAmount = $totalAmount - $billAmount;
        }

        $tipPerPerson = $tipAmount / $numPeople;
        $totalPerPerson = $totalAmount / $numPeople;

        $result = [
            'bill_amount' => round($billAmount, 2),
            'tip_percent' => round($tipPercent, 2),
            'tip_amount' => round($tipAmount, 2),
            'total_amount' => round($totalAmount, 2),
            'num_people' => $numPeople,
            'tip_per_person' => round($tipPerPerson, 2),
            'total_per_person' => round($totalPerPerson, 2)
        ];

        if ($billAmount > 0) {
            $this->saveHistory($result);
        }

        return $result;
    }

    private function saveHistory(array $data): void {
        $stmt = $this->db->prepare("INSERT INTO tip_history 
            (bill_amount, tip_percent, tip_amount, total_amount, num_people, total_per_person) 
            VALUES (:bill, :pct, :tip, :total, :people, :per_person)");
        $stmt->execute([
            ':bill' => $data['bill_amount'],
            ':pct' => $data['tip_percent'],
            ':tip' => $data['tip_amount'],
            ':total' => $data['total_amount'],
            ':people' => $data['num_people'],
            ':per_person' => $data['total_per_person']
        ]);
    }

    public function getHistory(int $limit = 5): array {
        $stmt = $this->db->prepare("SELECT * FROM tip_history ORDER BY id DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function clearHistory(): void {
        $this->db->exec("DELETE FROM tip_history");
    }
}

