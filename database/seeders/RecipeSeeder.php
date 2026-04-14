<?php

namespace Database\Seeders;

use App\Models\Recipes\Category;
use App\Models\Recipes\CookingMethod;
use App\Models\Recipes\Ingredient;
use App\Models\Recipes\Recipe;
use App\Models\Recipes\Tag;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        // ── Categorieën ───────────────────────────────────────────────────
        $categories = collect([
            ['name' => 'Vlees',   'color' => '#ef4444', 'icon' => 'heroicon-o-fire'],
            ['name' => 'Vis',     'color' => '#3b82f6', 'icon' => 'heroicon-o-beaker'],
            ['name' => 'Veggie',  'color' => '#22c55e', 'icon' => 'heroicon-o-sparkles'],
            ['name' => 'Pasta',   'color' => '#f59e0b', 'icon' => 'heroicon-o-circle-stack'],
            ['name' => 'Soep',    'color' => '#8b5cf6', 'icon' => 'heroicon-o-beaker'],
            ['name' => 'Dessert', 'color' => '#ec4899', 'icon' => 'heroicon-o-cake'],
            ['name' => 'Ontbijt', 'color' => '#f97316', 'icon' => 'heroicon-o-sun'],
            ['name' => 'Saus',    'color' => '#14b8a6', 'icon' => 'heroicon-o-beaker'],
        ])->mapWithKeys(fn ($c) => [
            $c['name'] => Category::firstOrCreate(['name' => $c['name']], $c),
        ]);

        // ── Kookmethodes ──────────────────────────────────────────────────
        $methods = collect([
            'Kookfuur', 'Oven', 'Cookeo', 'Tajine',
            'Airfryer', 'BBQ', 'Wok', 'Steamer',
        ])->mapWithKeys(fn ($name) => [
            $name => CookingMethod::firstOrCreate(['name' => $name]),
        ]);

        // ── Tags ──────────────────────────────────────────────────────────
        $tags = collect([
            'Glutenvrij', 'Lactosevrij', 'Snel (<30 min)',
            'Meal prep', 'Kindvriendelijk', 'Budget', 'Feestelijk',
        ])->mapWithKeys(fn ($name) => [
            $name => Tag::firstOrCreate(['name' => $name]),
        ]);

        // ── Ingrediënten ──────────────────────────────────────────────────
        $ings = collect([
            'Kippenbout', 'Ui', 'Knoflook', 'Tomaten', 'Olijfolie',
            'Zout', 'Peper', 'Paprika', 'Room', 'Boter',
            'Pasta', 'Parmezaan', 'Eieren', 'Spek', 'Zwarte peper',
        ])->mapWithKeys(fn ($name) => [
            $name => Ingredient::firstOrCreate(['name' => $name]),
        ]);

        // ── Recept 1: Kip Paprikash ───────────────────────────────────────
        $r1 = Recipe::firstOrCreate(
            ['slug' => 'kip-paprikash'],
            [
                'title'             => 'Kip Paprikash',
                'category_id'       => $categories['Vlees']->id,
                'cooking_method_id' => $methods['Kookfuur']->id,
                'description'       => 'Romige Hongaarse kippenstoofpot met paprika.',
                'instructions'      => [
                    ['step' => 'Snipper de ui fijn en fruit in olijfolie tot glazig.'],
                    ['step' => 'Voeg de knoflook en paprika toe en bak 1 minuut mee.'],
                    ['step' => 'Leg de kippenboutjes erbij en braad rondom aan.'],
                    ['step' => 'Voeg de tomaten toe, dek af en laat 35 min sudderen.'],
                    ['step' => 'Roer de room erdoor en breng op smaak met zout en peper.'],
                ],
                'prep_time'  => 15,
                'cook_time'  => 40,
                'servings'   => 4,
                'difficulty' => 'medium',
            ]
        );

        $r1->ingredients()->syncWithoutDetaching([
            $ings['Kippenbout']->id => ['quantity' => 4,   'unit' => 'stuk', 'sort_order' => 1],
            $ings['Ui']->id         => ['quantity' => 2,   'unit' => 'stuk', 'sort_order' => 2, 'notes' => 'fijngesneden'],
            $ings['Knoflook']->id   => ['quantity' => 3,   'unit' => 'stuk', 'sort_order' => 3],
            $ings['Paprika']->id    => ['quantity' => 2,   'unit' => 'el',   'sort_order' => 4, 'notes' => 'zoet'],
            $ings['Tomaten']->id    => ['quantity' => 400, 'unit' => 'g',    'sort_order' => 5],
            $ings['Room']->id       => ['quantity' => 150, 'unit' => 'ml',   'sort_order' => 6],
            $ings['Olijfolie']->id  => ['quantity' => 2,   'unit' => 'el',   'sort_order' => 7],
        ]);

        $r1->tags()->syncWithoutDetaching([
            $tags['Kindvriendelijk']->id,
            $tags['Meal prep']->id,
        ]);

        // ── Recept 2: Spaghetti Carbonara ────────────────────────────────
        $r2 = Recipe::firstOrCreate(
            ['slug' => 'spaghetti-carbonara'],
            [
                'title'             => 'Spaghetti Carbonara',
                'category_id'       => $categories['Pasta']->id,
                'cooking_method_id' => $methods['Kookfuur']->id,
                'description'       => 'De klassieke Italiaanse carbonara — zonder room!',
                'instructions'      => [
                    ['step' => 'Kook de pasta al dente in gezouten water.'],
                    ['step' => 'Bak het spek knapperig in een droge pan.'],
                    ['step' => 'Kluts eieren met geraspte parmezaan en zwarte peper.'],
                    ['step' => 'Meng de pasta met het spek, van het vuur, met het eimengsel.'],
                    ['step' => 'Voeg scheutjes kookwater toe tot romige saus.'],
                ],
                'prep_time'  => 10,
                'cook_time'  => 20,
                'servings'   => 4,
                'difficulty' => 'medium',
                'notes'      => 'Gebruik nooit room in een echte carbonara!',
            ]
        );

        $r2->ingredients()->syncWithoutDetaching([
            $ings['Pasta']->id        => ['quantity' => 400, 'unit' => 'g',    'sort_order' => 1],
            $ings['Spek']->id         => ['quantity' => 150, 'unit' => 'g',    'sort_order' => 2, 'notes' => 'guanciale of pancetta'],
            $ings['Eieren']->id       => ['quantity' => 4,   'unit' => 'stuk', 'sort_order' => 3, 'notes' => '2 heel + 2 dooiers'],
            $ings['Parmezaan']->id    => ['quantity' => 80,  'unit' => 'g',    'sort_order' => 4, 'notes' => 'fijn geraspt'],
            $ings['Zwarte peper']->id => ['quantity' => 1,   'unit' => 'tl',   'sort_order' => 5, 'notes' => 'versgemalen'],
        ]);

        $r2->tags()->syncWithoutDetaching([
            $tags['Snel (<30 min)']->id,
        ]);

        $this->command->info('✅ RecipeSeeder afgerond — categorieën, methodes, tags en 2 recepten aangemaakt.');
    }
}