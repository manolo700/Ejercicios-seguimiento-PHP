<?php
class Note {
    private PDO $db;

    public const CATEGORIES = [
        'Trabajo' => ['color' => 'blue', 'icon' => 'bi-briefcase'],
        'Personal' => ['color' => 'green', 'icon' => 'bi-person'],
        'Ideas' => ['color' => 'yellow', 'icon' => 'bi-lightbulb'],
        'Estudio' => ['color' => 'purple', 'icon' => 'bi-mortarboard'],
        'Urgente' => ['color' => 'red', 'icon' => 'bi-exclamation-triangle']
    ];

    public const COLORS = [
        'yellow' => ['bg' => '#fef9c3', 'border' => '#facc15', 'text' => '#713f12'],
        'blue' => ['bg' => '#e0f2fe', 'border' => '#38bdf8', 'text' => '#075985'],
        'green' => ['bg' => '#dcfce7', 'border' => '#4ade80', 'text' => '#14532d'],
        'purple' => ['bg' => '#f3e8ff', 'border' => '#c084fc', 'text' => '#581c87'],
        'red' => ['bg' => '#fee2e2', 'border' => '#f87171', 'text' => '#991b1b']
    ];

    public function __construct() {
        $dbPath = __DIR__ . '/../data/notes.sqlite';
        $this->db = new PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->initTable();
    }

    private function initTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS notes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            content TEXT NOT NULL,
            category TEXT NOT NULL,
            color TEXT DEFAULT 'yellow',
            is_pinned INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);

        // Preload sample notes if empty
        $count = (int)$this->db->query("SELECT COUNT(*) FROM notes")->fetchColumn();
        if ($count === 0) {
            $this->create('Proyecto Final PHP MVC', 'Implementar los 11 ejercicios con arquitectura modelo-vista-controlador completa y diseño responsivo.', 'Estudio', 'purple', true);
            $this->create('Idea de Aplicación Móvil', 'Crear una herramienta que sincronice recetas y listas de compras automáticamente.', 'Ideas', 'yellow', false);
            $this->create('Renovación de Licencia', 'Agendar cita antes de fin de mes para renovación de documentos personales.', 'Urgente', 'red', true);
            $this->create('Reunión de Sincronización', 'Presentar avances del sprint el próximo lunes a las 10:00 AM con el equipo técnico.', 'Trabajo', 'blue', false);
        }
    }

    public function getAll(string $search = '', string $category = ''): array {
        $sql = "SELECT * FROM notes WHERE 1=1";
        $params = [];

        if (trim($search) !== '') {
            $sql .= " AND (title LIKE :search OR content LIKE :search)";
            $params[':search'] = '%' . trim($search) . '%';
        }

        if (trim($category) !== '' && array_key_exists($category, self::CATEGORIES)) {
            $sql .= " AND category = :cat";
            $params[':cat'] = $category;
        }

        $sql .= " ORDER BY is_pinned DESC, updated_at DESC, id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM notes WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(string $title, string $content, string $category, string $color = 'yellow', bool $pinned = false): bool {
        if (trim($title) === '') return false;
        if (!array_key_exists($category, self::CATEGORIES)) $category = 'Personal';
        if (!array_key_exists($color, self::COLORS)) $color = 'yellow';

        $stmt = $this->db->prepare("INSERT INTO notes (title, content, category, color, is_pinned, updated_at) 
            VALUES (:title, :content, :category, :color, :pinned, CURRENT_TIMESTAMP)");
        return $stmt->execute([
            ':title' => trim($title),
            ':content' => trim($content),
            ':category' => $category,
            ':color' => $color,
            ':pinned' => $pinned ? 1 : 0
        ]);
    }

    public function update(int $id, string $title, string $content, string $category, string $color = 'yellow', bool $pinned = false): bool {
        if (trim($title) === '') return false;
        if (!array_key_exists($category, self::CATEGORIES)) $category = 'Personal';
        if (!array_key_exists($color, self::COLORS)) $color = 'yellow';

        $stmt = $this->db->prepare("UPDATE notes SET 
            title = :title, 
            content = :content, 
            category = :category, 
            color = :color, 
            is_pinned = :pinned,
            updated_at = CURRENT_TIMESTAMP
            WHERE id = :id");
        return $stmt->execute([
            ':id' => $id,
            ':title' => trim($title),
            ':content' => trim($content),
            ':category' => $category,
            ':color' => $color,
            ':pinned' => $pinned ? 1 : 0
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM notes WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function togglePin(int $id): bool {
        $stmt = $this->db->prepare("UPDATE notes SET is_pinned = 1 - is_pinned WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getCategoryStats(): array {
        $stmt = $this->db->query("SELECT category, COUNT(*) as count FROM notes GROUP BY category");
        $counts = [];
        while ($row = $stmt->fetch()) {
            $counts[$row['category']] = (int)$row['count'];
        }
        return $counts;
    }
}
