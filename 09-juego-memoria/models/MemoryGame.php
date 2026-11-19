<?php
/**
 * MemoryGame Model
 * Handles card generation, scoring logic, and leaderboard database operations.
 */
class MemoryGame {
    private PDO $db;

    public const ICONS = [
        ['icon' => '🍕', 'name' => 'Pizza', 'color' => '#f97316'],
        ['icon' => '🍔', 'name' => 'Burger', 'color' => '#eab308'],
        ['icon' => '🌮', 'name' => 'Taco', 'color' => '#ca8a04'],
        ['icon' => '🍣', 'name' => 'Sushi', 'color' => '#ef4444'],
        ['icon' => '🍩', 'name' => 'Donut', 'color' => '#ec4899'],
        ['icon' => '🥑', 'name' => 'Avocado', 'color' => '#22c55e'],
        ['icon' => '☕', 'name' => 'Coffee', 'color' => '#78350f'],
        ['icon' => '🍦', 'name' => 'Ice Cream', 'color' => '#06b6d4'],
        ['icon' => '🍓', 'name' => 'Strawberry', 'color' => '#e11d48'],
        ['icon' => '🍉', 'name' => 'Watermelon', 'color' => '#10b981'],
        ['icon' => '🍟', 'name' => 'Fries', 'color' => '#f59e0b'],
        ['icon' => '🍰', 'name' => 'Cake', 'color' => '#8b5cf6']
    ];

    public const LEVELS = [
        'facil' => ['name' => 'Fácil', 'pairs' => 6, 'cols' => 4],
        'medio' => ['name' => 'Medio', 'pairs' => 8, 'cols' => 4],
        'dificil' => ['name' => 'Difícil', 'pairs' => 12, 'cols' => 6]
    ];

    public function __construct() {
        $dbPath = __DIR__ . '/../data/memory.sqlite';
        $this->db = new PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->initializeTable();
    }

    private function initializeTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS high_scores (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            player_name TEXT NOT NULL,
            difficulty TEXT NOT NULL,
            moves INTEGER NOT NULL,
            time_seconds INTEGER NOT NULL,
            score INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);

        // Preload sample leaderboard records if empty
        $count = (int)$this->db->query("SELECT COUNT(*) FROM high_scores")->fetchColumn();
        if ($count === 0) {
            $this->saveScore('Valeria', 'facil', 9, 22, 5320);
            $this->saveScore('Mateo', 'medio', 14, 38, 6950);
            $this->saveScore('Alejandro', 'dificil', 24, 75, 9650);
        }
    }

    /**
     * Generate shuffled card deck for the specified difficulty level.
     *
     * @param string $difficulty
     * @return array
     */
    public function generateCards(string $difficulty): array {
        $level = self::LEVELS[$difficulty] ?? self::LEVELS['medio'];
        $numPairs = $level['pairs'];

        $selectedIcons = array_slice(self::ICONS, 0, $numPairs);
        $deck = [];

        foreach ($selectedIcons as $index => $item) {
            // Card A
            $deck[] = [
                'uid' => $index . '-a',
                'pair_id' => $index,
                'icon' => $item['icon'],
                'name' => $item['name'],
                'color' => $item['color']
            ];
            // Card B
            $deck[] = [
                'uid' => $index . '-b',
                'pair_id' => $index,
                'icon' => $item['icon'],
                'name' => $item['name'],
                'color' => $item['color']
            ];
        }

        // Randomize deck order
        shuffle($deck);
        return $deck;
    }

    /**
     * Save player score into high_scores table.
     */
    public function saveScore(string $playerName, string $difficulty, int $moves, int $timeSeconds, int $score): bool {
        $playerName = trim($playerName);
        if ($playerName === '') {
            $playerName = 'Jugador Anónimo';
        }

        $stmt = $this->db->prepare("INSERT INTO high_scores 
            (player_name, difficulty, moves, time_seconds, score) 
            VALUES (:player, :diff, :moves, :time, :score)");

        return $stmt->execute([
            ':player' => $playerName,
            ':diff' => $difficulty,
            ':moves' => $moves,
            ':time' => $timeSeconds,
            ':score' => $score
        ]);
    }

    /**
     * Retrieve leaderboard records sorted by highest score.
     */
    public function getLeaderboard(string $difficulty = 'all', int $limit = 10): array {
        $sql = "SELECT * FROM high_scores";
        $params = [];

        if ($difficulty !== 'all' && array_key_exists($difficulty, self::LEVELS)) {
            $sql .= " WHERE difficulty = :diff";
            $params[':diff'] = $difficulty;
        }

        $sql .= " ORDER BY score DESC, time_seconds ASC, moves ASC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $paramKey => $paramVal) {
            $stmt->bindValue($paramKey, $paramVal);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
