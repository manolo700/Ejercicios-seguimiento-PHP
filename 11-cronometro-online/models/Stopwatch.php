<?php
class Stopwatch {
    private PDO $db;

    public function __construct() {
        $dbPath = __DIR__ . '/../data/stopwatch.sqlite';
        $this->db = new PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->initTables();
    }

    private function initTables(): void {
        $this->db->exec("CREATE TABLE IF NOT EXISTS stopwatch_sessions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            session_name TEXT NOT NULL,
            total_time_ms INTEGER NOT NULL,
            total_laps INTEGER NOT NULL,
            fastest_lap_ms INTEGER,
            slowest_lap_ms INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $this->db->exec("CREATE TABLE IF NOT EXISTS stopwatch_laps (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            session_id INTEGER NOT NULL,
            lap_number INTEGER NOT NULL,
            lap_time_ms INTEGER NOT NULL,
            overall_time_ms INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (session_id) REFERENCES stopwatch_sessions(id) ON DELETE CASCADE
        )");

        // Preload sample session if empty
        $count = (int)$this->db->query("SELECT COUNT(*) FROM stopwatch_sessions")->fetchColumn();
        if ($count === 0) {
            $sampleLaps = [
                ['number' => 1, 'lap_ms' => 45230, 'overall_ms' => 45230],
                ['number' => 2, 'lap_ms' => 42110, 'overall_ms' => 87340],
                ['number' => 3, 'lap_ms' => 47890, 'overall_ms' => 135230]
            ];
            $this->saveSession('Sesión de Entrenamiento 1', 135230, $sampleLaps);
        }
    }

    public function saveSession(string $name, int $totalTimeMs, array $laps): int {
        $name = trim($name) ?: ('Sesión #' . date('d/m H:i'));
        $totalLaps = count($laps);

        $fastest = null;
        $slowest = null;
        if ($totalLaps > 0) {
            $times = array_column($laps, 'lap_ms');
            $fastest = min($times);
            $slowest = max($times);
        }

        $stmt = $this->db->prepare("INSERT INTO stopwatch_sessions 
            (session_name, total_time_ms, total_laps, fastest_lap_ms, slowest_lap_ms) 
            VALUES (:name, :total, :laps, :fastest, :slowest)");
        $stmt->execute([
            ':name' => $name,
            ':total' => $totalTimeMs,
            ':laps' => $totalLaps,
            ':fastest' => $fastest,
            ':slowest' => $slowest
        ]);

        $sessionId = (int)$this->db->lastInsertId();

        if ($totalLaps > 0) {
            $stmtLap = $this->db->prepare("INSERT INTO stopwatch_laps 
                (session_id, lap_number, lap_time_ms, overall_time_ms) 
                VALUES (:sid, :num, :ltime, :otime)");
            foreach ($laps as $l) {
                $stmtLap->execute([
                    ':sid' => $sessionId,
                    ':num' => (int)$l['number'],
                    ':ltime' => (int)$l['lap_ms'],
                    ':otime' => (int)$l['overall_ms']
                ]);
            }
        }

        return $sessionId;
    }

    public function getPastSessions(int $limit = 5): array {
        $stmt = $this->db->prepare("SELECT * FROM stopwatch_sessions ORDER BY id DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $sessions = $stmt->fetchAll();

        foreach ($sessions as &$s) {
            $stmtLaps = $this->db->prepare("SELECT * FROM stopwatch_laps WHERE session_id = :sid ORDER BY lap_number ASC");
            $stmtLaps->execute([':sid' => $s['id']]);
            $s['laps'] = $stmtLaps->fetchAll();
        }
        return $sessions;
    }

    public function clearHistory(): void {
        $this->db->exec("DELETE FROM stopwatch_laps");
        $this->db->exec("DELETE FROM stopwatch_sessions");
    }

    public static function formatMs(int $ms): string {
        $hours = floor($ms / 3600000);
        $minutes = floor(($ms % 3600000) / 60000);
        $seconds = floor(($ms % 60000) / 1000);
        $millis = floor(($ms % 1000) / 10);

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d.%02d', $hours, $minutes, $seconds, $millis);
        }
        return sprintf('%02d:%02d.%02d', $minutes, $seconds, $millis);
    }
}

