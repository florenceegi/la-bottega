<?php

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Seed configurazione base: opportunita demo, strumenti registrati
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BottegaConfigSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSampleOpportunities();
    }

    private function seedSampleOpportunities(): void
    {
        $opportunities = [
            [
                'title' => 'Firenze Art Residency — Open Call Emerging Artists',
                'type' => 'residency',
                'deadline' => '2026-06-30',
                'url' => 'https://example.com/firenze-residency',
                'requirements' => json_encode(['portfolio_min_works' => 10, 'cv_required' => true]),
                'mediums_accepted' => json_encode(['painting', 'sculpture', 'mixed_media']),
                'career_level_min' => 'emerging',
                'career_level_max' => 'mid_career',
                'country' => 'IT',
                'description' => 'Residenza artistica di 3 mesi a Firenze per artisti emergenti e mid-career.',
                'source' => 'manual_seed',
                'verified' => true,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Premio Arte Contemporanea Toscana 2026',
                'type' => 'award',
                'deadline' => '2026-09-15',
                'url' => 'https://example.com/premio-toscana',
                'requirements' => json_encode(['age_max' => 40, 'portfolio_min_works' => 5]),
                'mediums_accepted' => json_encode(['painting', 'photography', 'digital_art', 'sculpture']),
                'career_level_min' => 'emerging',
                'career_level_max' => 'established',
                'country' => 'IT',
                'description' => 'Premio biennale per artisti contemporanei residenti in Toscana.',
                'source' => 'manual_seed',
                'verified' => true,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Berlin Art Fair — Call for Artists 2026',
                'type' => 'fair',
                'deadline' => '2026-07-01',
                'url' => 'https://example.com/berlin-art-fair',
                'requirements' => json_encode(['gallery_representation' => false, 'portfolio_min_works' => 8]),
                'mediums_accepted' => json_encode(['painting', 'photography', 'installation', 'video_art']),
                'career_level_min' => 'mid_career',
                'career_level_max' => 'established',
                'country' => 'DE',
                'description' => 'Fiera d\'arte contemporanea aperta ad artisti indipendenti senza rappresentanza galleria.',
                'source' => 'manual_seed',
                'verified' => false,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('bottega.opportunities')->insertOrIgnore($opportunities);
    }
}
