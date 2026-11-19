<?php
class Recipe {
    private PDO $db;

    public const MEAL_TYPES = ['Desayuno', 'Almuerzo', 'Cena', 'Postre', 'Snack'];
    public const DIFFICULTIES = ['Fácil', 'Media', 'Avanzada'];

    public function __construct() {
        $dbPath = __DIR__ . '/../data/recipes.sqlite';
        $this->db = new PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->initTable();
    }

    private function initTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS recipes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            ingredients TEXT NOT NULL,
            instructions TEXT NOT NULL,
            meal_type TEXT NOT NULL,
            difficulty TEXT DEFAULT 'Media',
            prep_time_minutes INTEGER DEFAULT 30,
            servings INTEGER DEFAULT 4,
            image_url TEXT,
            author_name TEXT DEFAULT 'Chef de la Comunidad',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);

        // Preload sample recipes if empty
        $count = (int)$this->db->query("SELECT COUNT(*) FROM recipes")->fetchColumn();
        if ($count === 0) {
            $this->create([
                'title' => 'Tacos al Pastor Caseros',
                'description' => 'Deliciosos tacos de cerdo adobados con achiote, especias mexicanas y piña asada.',
                'ingredients' => "1 kg de lomo o pierna de cerdo cortada fina\n3 cucharadas de pasta de achiote\n2 chiles guajillo hidratados\n1/4 taza de jugo de naranja\n1/4 taza de vinagre blanco\n1 taza de piña fresca en rebanadas\nTortillas de maíz calientes\nCilantro fresco y cebolla picada",
                'instructions' => "1. Licúa el achiote, chiles guajillo, jugo de naranja, vinagre, ajo y orégano.\n2. Marina la carne de cerdo durante al menos 2 horas o toda la noche.\n3. Cocina la carne a fuego alto en sartén o comal con un poco de aceite hasta dorar.\n4. Asa las rebanadas de piña hasta caramelizar.\n5. Pica la carne y sirve sobre tortillas calientes con piña, cilantro, cebolla y salsa.",
                'meal_type' => 'Cena',
                'difficulty' => 'Media',
                'prep_time_minutes' => 45,
                'servings' => 4,
                'image_url' => 'https://images.unsplash.com/photo-1551504734-5ee1c4a1479b?w=600&auto=format&fit=crop&q=80',
                'author_name' => 'Chef Rodrigo'
            ]);

            $this->create([
                'title' => 'Pasta a la Carbonara Auténtica',
                'description' => 'La auténtica receta romana sin crema de leche, usando solo yemas de huevo y pecorino.',
                'ingredients' => "400g de espaguetis de sémola de trigo duro\n150g de guanciale o panceta curada\n4 yemas de huevo frescas + 1 huevo entero\n80g de queso Pecorino Romano rallado fino\nPimienta negra recién molida abundante\nSal para el agua de la pasta",
                'instructions' => "1. Hierve agua con sal moderada y cuece la pasta al dente.\n2. En sartén amplia dora la panceta cortada en tiras hasta quedar crujiente.\n3. Bate las yemas con el huevo entero, pimienta negra y casi todo el pecorino hasta formar una crema.\n4. Pasa los espaguetis a la sartén con fuego apagado, agrega un cucharón de agua de cocción.\n5. Añade la crema de huevo batiendo enérgicamente para emulsionar sin cuajar.",
                'meal_type' => 'Almuerzo',
                'difficulty' => 'Media',
                'prep_time_minutes' => 25,
                'servings' => 4,
                'image_url' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=600&auto=format&fit=crop&q=80',
                'author_name' => 'Chef Marco'
            ]);

            $this->create([
                'title' => 'Ensalada César con Pollo Grill',
                'description' => 'Fresca, crujiente y coronada con aderezo casero de anchoas, limón y parmesano.',
                'ingredients' => "2 pechugas de pollo a la plancha fileteadas\n2 piezas de lechuga romana fresca y crujiente\n1 taza de croutones tostados al ajo\n50g de queso parmesano en lajas\n3 cucharadas de mayonesa de calidad\n1 cucharadita de mostaza Dijon\n1 diente de ajo machacado y jugo de medio limón",
                'instructions' => "1. Lava y trocea la lechuga romana con las manos, secándola bien.\n2. Mezcla la mayonesa, mostaza Dijon, ajo, jugo de limón y pimienta para el aderezo.\n3. Asa las pechugas sazonadas con sal y pimienta, luego córtalas en tiras.\n4. En un bol amplio mezcla la lechuga con el aderezo, agrega pollo, croutones y queso parmesano.",
                'meal_type' => 'Almuerzo',
                'difficulty' => 'Fácil',
                'prep_time_minutes' => 20,
                'servings' => 2,
                'image_url' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&auto=format&fit=crop&q=80',
                'author_name' => 'Chef Valentina'
            ]);

            $this->create([
                'title' => 'Brownies Fudgy de Chocolate Intenso',
                'description' => 'Interior húmedo y denso con corteza brillante y crujiente para amantes del cacao.',
                'ingredients' => "200g de chocolate negro (70% cacao)\n150g de mantequilla sin sal\n200g de azúcar moreno o blanca\n3 huevos a temperatura ambiente\n75g de harina de trigo\n30g de cacao puro en polvo\nUna pizca de flor de sal",
                'instructions' => "1. Precalienta el horno a 175°C y forra un molde cuadrado con papel de hornear.\n2. Derrite el chocolate junto a la mantequilla a baño maría.\n3. Bate los huevos con el azúcar durante 4 minutos hasta blanquear y doblar volumen.\n4. Incorpora el chocolate derretido y luego tamiza la harina y el cacao con movimientos suaves.\n5. Hornea de 22 a 25 minutos. Deja enfriar antes de cortar.",
                'meal_type' => 'Postre',
                'difficulty' => 'Fácil',
                'prep_time_minutes' => 35,
                'servings' => 8,
                'image_url' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&auto=format&fit=crop&q=80',
                'author_name' => 'Chef Sofía'
            ]);
        }
    }

    public function getAll(string $search = '', string $mealType = '', string $difficulty = ''): array {
        $sql = "SELECT * FROM recipes WHERE 1=1";
        $params = [];

        if (trim($search) !== '') {
            $sql .= " AND (title LIKE :search OR description LIKE :search OR ingredients LIKE :search)";
            $params[':search'] = '%' . trim($search) . '%';
        }

        if (trim($mealType) !== '' && in_array($mealType, self::MEAL_TYPES)) {
            $sql .= " AND meal_type = :meal";
            $params[':meal'] = $mealType;
        }

        if (trim($difficulty) !== '' && in_array($difficulty, self::DIFFICULTIES)) {
            $sql .= " AND difficulty = :diff";
            $params[':diff'] = $difficulty;
        }

        $sql .= " ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM recipes WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): bool {
        $img = trim($data['image_url'] ?? '');
        if (empty($img)) {
            $img = 'https://images.unsplash.com/photo-1495521821757-a1efb6729352?w=600&auto=format&fit=crop&q=80';
        }

        $stmt = $this->db->prepare("INSERT INTO recipes 
            (title, description, ingredients, instructions, meal_type, difficulty, prep_time_minutes, servings, image_url, author_name)
            VALUES (:title, :description, :ingredients, :instructions, :meal_type, :difficulty, :prep, :servings, :img, :author)");
        
        return $stmt->execute([
            ':title' => trim($data['title']),
            ':description' => trim($data['description'] ?? ''),
            ':ingredients' => trim($data['ingredients']),
            ':instructions' => trim($data['instructions']),
            ':meal_type' => $data['meal_type'] ?? 'Almuerzo',
            ':difficulty' => $data['difficulty'] ?? 'Media',
            ':prep' => max(5, (int)($data['prep_time_minutes'] ?? 30)),
            ':servings' => max(1, (int)($data['servings'] ?? 4)),
            ':img' => $img,
            ':author' => trim($data['author_name'] ?? 'Chef Anónimo')
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM recipes WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getStats(): array {
        $total = (int)$this->db->query("SELECT COUNT(*) FROM recipes")->fetchColumn();
        return ['total' => $total];
    }
}

