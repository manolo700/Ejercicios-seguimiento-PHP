<?php
/**
 * Recipe Model
 * Manages culinary recipes, search filters, and recipe submissions.
 */
class Recipe {
    private PDO $db;

    public const MEAL_TYPES = ['Breakfast', 'Lunch', 'Dinner', 'Dessert', 'Snack'];
    public const DIFFICULTIES = ['Easy', 'Medium', 'Advanced'];

    public function __construct() {
        $dbPath = __DIR__ . '/../data/recipes.sqlite';
        $this->db = new PDO('sqlite:' . $dbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->initializeTable();
    }

    private function initializeTable(): void {
        $sql = "CREATE TABLE IF NOT EXISTS recipes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT NOT NULL,
            ingredients TEXT NOT NULL,
            instructions TEXT NOT NULL,
            meal_type TEXT NOT NULL,
            difficulty TEXT DEFAULT 'Medium',
            prep_time_minutes INTEGER DEFAULT 30,
            servings INTEGER DEFAULT 4,
            image_url TEXT,
            author_name TEXT DEFAULT 'Community Chef',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->exec($sql);

        // Preload sample recipes if empty
        $count = (int)$this->db->query("SELECT COUNT(*) FROM recipes")->fetchColumn();
        if ($count === 0) {
            $this->create([
                'title' => 'Homemade Street Tacos',
                'description' => 'Delicious marinated pork tacos with Mexican spices, fresh cilantro, onions, and roasted pineapple.',
                'ingredients' => "1 kg pork shoulder sliced thin\n3 tbsp achiote paste\n2 guajillo chiles softened\n1/4 cup orange juice\n1/4 cup white vinegar\n1 cup fresh pineapple sliced\nWarm corn tortillas\nFresh cilantro and chopped onion",
                'instructions' => "1. Blend achiote, chiles, orange juice, vinegar, garlic, and oregano for marinade.\n2. Marinate pork for at least 2 hours or overnight.\n3. Cook pork over high heat in skillet until browned and caramelized.\n4. Grill pineapple slices until sweet and tender.\n5. Chop pork and serve on warm corn tortillas topped with pineapple, cilantro, onion, and lime.",
                'meal_type' => 'Dinner',
                'difficulty' => 'Medium',
                'prep_time_minutes' => 45,
                'servings' => 4,
                'image_url' => 'https://images.unsplash.com/photo-1551504734-5ee1c4a1479b?w=600&auto=format&fit=crop&q=80',
                'author_name' => 'Chef Rodrigo'
            ]);

            $this->create([
                'title' => 'Authentic Roman Carbonara',
                'description' => 'Traditional Italian recipe made without cream, using fresh egg yolks, crispy guanciale, and aged Pecorino Romano.',
                'ingredients' => "400g bronze-cut spaghetti\n150g cured guanciale or pancetta\n4 fresh egg yolks + 1 whole egg\n80g finely grated Pecorino Romano cheese\nFreshly cracked black pepper\nSalt for pasta water",
                'instructions' => "1. Bring salted water to boil and cook spaghetti al dente.\n2. In a skillet, fry guanciale cubes over medium heat until crispy.\n3. Whisk egg yolks, whole egg, cracked black pepper, and Pecorino until thick paste forms.\n4. Transfer spaghetti to skillet with heat turned off and add a ladle of starchy pasta water.\n5. Pour egg cream while tossing vigorously to emulsify into a silky sauce.",
                'meal_type' => 'Lunch',
                'difficulty' => 'Medium',
                'prep_time_minutes' => 25,
                'servings' => 4,
                'image_url' => 'https://images.unsplash.com/photo-1612874742237-6526221588e3?w=600&auto=format&fit=crop&q=80',
                'author_name' => 'Chef Marco'
            ]);

            $this->create([
                'title' => 'Classic Chicken Caesar Salad',
                'description' => 'Crisp romaine lettuce, grilled chicken breast, garlic herb croutons, and creamy homemade Caesar dressing.',
                'ingredients' => "2 grilled chicken breasts sliced\n2 heads of fresh romaine lettuce\n1 cup garlic toasted croutons\n50g shaved Parmesan cheese\n3 tbsp quality mayonnaise\n1 tsp Dijon mustard\n1 minced garlic clove and juice of half a lemon",
                'instructions' => "1. Wash, dry, and tear romaine lettuce leaves into bite-sized pieces.\n2. Whisk mayonnaise, Dijon mustard, minced garlic, lemon juice, and black pepper for dressing.\n3. Season and grill chicken breasts, then slice into strips.\n4. Toss lettuce with dressing in a large bowl, top with chicken, croutons, and Parmesan shavings.",
                'meal_type' => 'Lunch',
                'difficulty' => 'Easy',
                'prep_time_minutes' => 20,
                'servings' => 2,
                'image_url' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=600&auto=format&fit=crop&q=80',
                'author_name' => 'Chef Valentina'
            ]);

            $this->create([
                'title' => 'Fudgy Dark Chocolate Brownies',
                'description' => 'Rich, dense, and fudgy center with shiny crinkly tops, made with 70% dark chocolate.',
                'ingredients' => "200g dark chocolate (70% cocoa)\n150g unsalted butter\n200g brown or cane sugar\n3 eggs at room temperature\n75g all-purpose flour\n30g pure unsweetened cocoa powder\nA pinch of sea salt",
                'instructions' => "1. Preheat oven to 175°C (350°F) and line a square baking tin with parchment paper.\n2. Melt dark chocolate and butter gently in a heatproof bowl over simmering water.\n3. Beat eggs and sugar with hand mixer for 4 minutes until pale and doubled in volume.\n4. Fold in melted chocolate mixture, then sift flour and cocoa powder gently.\n5. Bake for 22-25 minutes. Allow to cool completely before slicing into squares.",
                'meal_type' => 'Dessert',
                'difficulty' => 'Easy',
                'prep_time_minutes' => 35,
                'servings' => 8,
                'image_url' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&auto=format&fit=crop&q=80',
                'author_name' => 'Chef Sofia'
            ]);
        }
    }

    /**
     * Retrieve recipes with search, meal type, and difficulty filters.
     */
    public function getAll(string $search = '', string $mealType = '', string $difficulty = ''): array {
        $sql = "SELECT * FROM recipes WHERE 1=1";
        $params = [];

        if (trim($search) !== '') {
            $sql .= " AND (title LIKE :search OR description LIKE :search OR ingredients LIKE :search)";
            $params[':search'] = '%' . trim($search) . '%';
        }

        if (trim($mealType) !== '') {
            $sql .= " AND (meal_type = :meal OR meal_type LIKE :meal_like)";
            $params[':meal'] = $mealType;
            $params[':meal_like'] = '%' . $mealType . '%';
        }

        if (trim($difficulty) !== '') {
            $sql .= " AND (difficulty = :diff OR difficulty LIKE :diff_like)";
            $params[':diff'] = $difficulty;
            $params[':diff_like'] = '%' . $difficulty . '%';
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
            ':meal_type' => $data['meal_type'] ?? 'Lunch',
            ':difficulty' => $data['difficulty'] ?? 'Medium',
            ':prep' => max(5, (int)($data['prep_time_minutes'] ?? 30)),
            ':servings' => max(1, (int)($data['servings'] ?? 4)),
            ':img' => $img,
            ':author' => trim($data['author_name'] ?? 'Community Chef')
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
