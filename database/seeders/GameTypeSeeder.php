<?php

namespace Database\Seeders;

use App\Models\GameType;
use Illuminate\Database\Seeder;

class GameTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Uno',
                'slug' => 'uno',
                'lowest_score_wins' => true,
                'min_players' => 2,
                'max_players' => 10,
                'description' => 'Klassiek kaartspel. Laagste score wint.',
            ],
            [
                'name' => 'Kaarten',
                'slug' => 'kaarten',
                'lowest_score_wins' => false,
                'min_players' => 2,
                'max_players' => null,
                'description' => 'Algemeen kaartspel. Hoogste score wint.',
            ],
        ];

        foreach ($types as $type) {
            GameType::updateOrCreate(['slug' => $type['slug']], $type);
        }
    }
}
