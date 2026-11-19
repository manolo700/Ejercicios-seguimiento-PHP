<?php
class Task {
    private PDO $db;

    public function __construct() {
        $dbPath = __DIR__ . '/../data/tasks.sqlite';
        $this->db = new PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->initTable();
    }

    private function initTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS tasks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            priority TEXT DEFAULT 'media',
            completed INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);
    }

    public function getAll(string $filter = 'all'): array {
        $sql = "SELECT * FROM tasks";
        if ($filter === 'pending') {
            $sql .= " WHERE completed = 0";
        } elseif ($filter === 'completed') {
            $sql .= " WHERE completed = 1";
        }
        $sql .= " ORDER BY completed ASC, 
                  CASE priority 
                    WHEN 'alta' THEN 1 
                    WHEN 'media' THEN 2 
                    WHEN 'baja' THEN 3 
                    ELSE 4 
                  END ASC, id DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function add(string $title, string $priority = 'media'): bool {
        $title = trim($title);
        if ($title === '') {
            return false;
        }
        $validPriorities = ['alta', 'media', 'baja'];
        if (!in_array($priority, $validPriorities)) {
            $priority = 'media';
        }

        $stmt = $this->db->prepare("INSERT INTO tasks (title, priority, completed) VALUES (:title, :priority, 0)");
        return $stmt->execute([
            ':title' => $title,
            ':priority' => $priority
        ]);
    }

    public function toggle(int $id): bool {
        $stmt = $this->db->prepare("UPDATE tasks SET completed = 1 - completed WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function clearCompleted(): bool {
        return (bool)$this->db->exec("DELETE FROM tasks WHERE completed = 1");
    }

    public function getStats(): array {
        $total = (int)$this->db->query("SELECT COUNT(*) FROM tasks")->fetchColumn();
        $completed = (int)$this->db->query("SELECT COUNT(*) FROM tasks WHERE completed = 1")->fetchColumn();
        $pending = $total - $completed;
        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending
        ];
    }
}

