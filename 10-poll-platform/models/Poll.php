<?php
/**
 * Poll Model
 * Manages survey polls, answer options, session-based votes, and statistical reports.
 */
class Poll {
    private PDO $db;

    public function __construct() {
        $dbPath = __DIR__ . '/../data/polls.sqlite';
        $this->db = new PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->initTables();
    }

    private function initTables(): void {
        $this->db->exec("CREATE TABLE IF NOT EXISTS polls (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            category TEXT DEFAULT 'Technology',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS poll_options (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            poll_id INTEGER NOT NULL,
            option_text TEXT NOT NULL,
            votes INTEGER DEFAULT 0,
            FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS poll_votes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            poll_id INTEGER NOT NULL,
            option_id INTEGER NOT NULL,
            session_token TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Preload sample polls if database is newly initialized
        $count = (int)$this->db->query("SELECT COUNT(*) FROM polls")->fetchColumn();
        if ($count === 0) {
            $p1 = $this->create(
                'What is your preferred backend technology for web development?',
                'Community survey regarding modern developer tools and server-side ecosystems.',
                'Technology',
                ['PHP (Modern / Laravel / MVC)', 'JavaScript (Node.js / Bun)', 'Python (FastAPI / Django)', 'Go (Golang)', 'Java (Spring Boot)']
            );
            $this->addDummyVotes($p1, [45, 38, 29, 21, 15]);

            $p2 = $this->create(
                'What is your ideal work arrangement?',
                'Assessment of current workplace preferences in the software engineering sector.',
                'Work',
                ['100% Fully Remote', 'Hybrid (2-3 days in office)', '100% On-site Office']
            );
            $this->addDummyVotes($p2, [72, 34, 6]);
        }
    }

    private function addDummyVotes(int $pollId, array $voteCounts): void {
        $stmt = $this->db->prepare("SELECT id FROM poll_options WHERE poll_id = :pid ORDER BY id ASC");
        $stmt->execute([':pid' => $pollId]);
        $optIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($optIds as $idx => $optId) {
            $vCount = $voteCounts[$idx] ?? 0;
            if ($vCount > 0) {
                $up = $this->db->prepare("UPDATE poll_options SET votes = :votes WHERE id = :id");
                $up->execute([':votes' => $vCount, ':id' => $optId]);
            }
        }
    }

    public function getAll(): array {
        $sql = "SELECT p.*, COALESCE(SUM(o.votes), 0) as total_votes, COUNT(o.id) as option_count
                FROM polls p
                LEFT JOIN poll_options o ON p.id = o.poll_id
                GROUP BY p.id
                ORDER BY p.id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM polls WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $poll = $stmt->fetch();
        if (!$poll) return null;

        $stmtOpts = $this->db->prepare("SELECT * FROM poll_options WHERE poll_id = :id ORDER BY id ASC");
        $stmtOpts->execute([':id' => $id]);
        $options = $stmtOpts->fetchAll();

        $totalVotes = 0;
        foreach ($options as $opt) {
            $totalVotes += (int)$opt['votes'];
        }

        foreach ($options as &$opt) {
            $opt['percent'] = $totalVotes > 0 ? round(($opt['votes'] / $totalVotes) * 100, 1) : 0;
        }

        $poll['options'] = $options;
        $poll['total_votes'] = $totalVotes;
        return $poll;
    }

    public function create(string $title, string $description, string $category, array $options): int {
        $stmt = $this->db->prepare("INSERT INTO polls (title, description, category) VALUES (:title, :desc, :cat)");
        $stmt->execute([
            ':title' => trim($title),
            ':desc' => trim($description),
            ':cat' => trim($category) ?: 'General'
        ]);
        $pollId = (int)$this->db->lastInsertId();

        $stmtOpt = $this->db->prepare("INSERT INTO poll_options (poll_id, option_text, votes) VALUES (:pid, :text, 0)");
        foreach ($options as $opt) {
            $optText = trim($opt);
            if ($optText !== '') {
                $stmtOpt->execute([':pid' => $pollId, ':text' => $optText]);
            }
        }

        return $pollId;
    }

    public function vote(int $pollId, int $optionId, string $sessionToken): bool {
        if ($this->hasVoted($pollId, $sessionToken)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE poll_options SET votes = votes + 1 WHERE id = :id AND poll_id = :pid");
        $success = $stmt->execute([':id' => $optionId, ':pid' => $pollId]);

        if ($success) {
            $stmtVote = $this->db->prepare("INSERT INTO poll_votes (poll_id, option_id, session_token) VALUES (:pid, :oid, :token)");
            $stmtVote->execute([':pid' => $pollId, ':oid' => $optionId, ':token' => $sessionToken]);
        }
        return $success;
    }

    public function hasVoted(int $pollId, string $sessionToken): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM poll_votes WHERE poll_id = :pid AND session_token = :token");
        $stmt->execute([':pid' => $pollId, ':token' => $sessionToken]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function delete(int $id): bool {
        $this->db->prepare("DELETE FROM poll_votes WHERE poll_id = :id")->execute([':id' => $id]);
        $this->db->prepare("DELETE FROM poll_options WHERE poll_id = :id")->execute([':id' => $id]);
        $stmt = $this->db->prepare("DELETE FROM polls WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
