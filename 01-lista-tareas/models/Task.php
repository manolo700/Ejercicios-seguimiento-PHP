<?php
/**
 * Task Model
 * Manages task persistence and status in SQLite database.
 */
class Task {
    private PDO $db;

    public function __construct() {
        $dbPath = __DIR__ . '/../data/tasks.sqlite';
        $this->db = new PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->initializeTable();
    }

    private function initializeTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS tasks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            priority TEXT DEFAULT 'medium',
            completed INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);

        // Preload sample tasks if empty
        $count = (int)$this->db->query("SELECT COUNT(*) FROM tasks")->fetchColumn();
        if ($count === 0) {
            $this->add('Complete PHP MVC web project', 'high');
            $this->add('Review unit test coverage and clean code principles', 'medium');
            $this->add('Prepare final project documentation and slides', 'low');
        }
    }

    /**
     * Get all tasks filtered by status.
     */
    public function getAll(string $filter = 'all'): array {
        $sql = "SELECT * FROM tasks";
        if ($filter === 'pending') {
            $sql .= " WHERE completed = 0";
        } elseif ($filter === 'completed') {
            $sql .= " WHERE completed = 1";
        }
        $sql .= " ORDER BY completed ASC, 
                  CASE priority 
                    WHEN 'high' THEN 1 
                    WHEN 'alta' THEN 1
                    WHEN 'medium' THEN 2 
                    WHEN 'media' THEN 2
                    WHEN 'low' THEN 3 
                    WHEN 'baja' THEN 3
                    ELSE 4 
                  END ASC, id DESC";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Add a new task.
     */
    public function add(string $title, string $priority = 'medium'): bool {
        $title = trim($title);
        if ($title === '') {
            return false;
        }

        $validPriorities = ['high', 'medium', 'low', 'alta', 'media', 'baja'];
        if (!in_array($priority, $validPriorities)) {
            $priority = 'medium';
        }

        $stmt = $this->db->prepare("INSERT INTO tasks (title, priority, completed) VALUES (:title, :priority, 0)");
        return $stmt->execute([
            ':title' => $title,
            ':priority' => $priority
        ]);
    }

    /**
     * Toggle task completion status.
     */
    public function toggle(int $id): bool {
        $stmt = $this->db->prepare("UPDATE tasks SET completed = 1 - completed WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Delete a task by ID.
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Delete all completed tasks.
     */
    public function clearCompleted(): bool {
        return (bool)$this->db->exec("DELETE FROM tasks WHERE completed = 1");
    }

    /**
     * Get total, completed, and pending counts.
     */
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
