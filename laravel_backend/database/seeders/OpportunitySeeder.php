<?php

declare(strict_types=1);

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Seed opportunita demo per Binocolo C.1 — call, residenze, fiere, premi.
 */

namespace Database\Seeders;

use App\Models\Opportunity;
use Illuminate\Database\Seeder;

class OpportunitySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $opportunities = [
            [
                'title' => 'Premio Cairo 2026',
                'type' => 'prize',
                'deadline' => $now->copy()->addDays(45),
                'url' => 'https://www.premiocairo.it',
                'mediums_accepted' => ['painting', 'drawing', 'photography', 'sculpture', 'installation'],
                'career_level_min' => 'emerging',
                'career_level_max' => 'mid',
                'country' => 'IT',
                'description' => 'Premio annuale per artisti under 40 residenti in Italia. Selezione di 20 finalisti esposti presso Palazzo Reale di Milano.',
                'source' => 'premiocairo.it',
                'verified' => true,
                'active' => true,
            ],
            [
                'title' => 'Bocs Art Residency Cosenza',
                'type' => 'residency',
                'deadline' => $now->copy()->addDays(90),
                'url' => 'https://www.bocsart.it',
                'mediums_accepted' => ['painting', 'sculpture', 'installation', 'video', 'performance'],
                'career_level_min' => 'emerging',
                'career_level_max' => 'established',
                'country' => 'IT',
                'description' => 'Residenza artistica di 3 mesi a Cosenza. Include alloggio, studio, stipendio mensile e mostra finale.',
                'source' => 'bocsart.it',
                'verified' => true,
                'active' => true,
            ],
            [
                'title' => 'MIA Photo Fair 2026 — Open Call',
                'type' => 'fair',
                'deadline' => $now->copy()->addDays(60),
                'url' => 'https://www.miafair.it',
                'mediums_accepted' => ['photography'],
                'career_level_min' => 'mid',
                'career_level_max' => 'established',
                'country' => 'IT',
                'description' => 'Fiera internazionale di fotografia a Milano. Open call per progetti inediti.',
                'source' => 'miafair.it',
                'verified' => true,
                'active' => true,
            ],
            [
                'title' => 'Rome Art Week 2026',
                'type' => 'event',
                'deadline' => $now->copy()->addDays(120),
                'url' => 'https://www.romeartweek.com',
                'mediums_accepted' => [],
                'career_level_min' => null,
                'career_level_max' => null,
                'country' => 'IT',
                'description' => 'Settimana dell\'arte contemporanea a Roma. Partecipazione aperta a artisti, gallerie e spazi indipendenti.',
                'source' => 'romeartweek.com',
                'verified' => true,
                'active' => true,
            ],
            [
                'title' => 'Artissima Torino — Present Future',
                'type' => 'fair',
                'deadline' => $now->copy()->addDays(75),
                'url' => 'https://www.artissima.art',
                'mediums_accepted' => ['painting', 'sculpture', 'installation', 'video', 'photography'],
                'career_level_min' => 'emerging',
                'career_level_max' => 'emerging',
                'country' => 'IT',
                'description' => 'Sezione dedicata ad artisti emergenti alla fiera Artissima di Torino. Selezione curata.',
                'source' => 'artissima.art',
                'verified' => true,
                'active' => true,
            ],
            [
                'title' => 'Villa Lena Residency Toscana',
                'type' => 'residency',
                'deadline' => $now->copy()->addDays(150),
                'url' => 'https://villa-lena.it',
                'mediums_accepted' => ['painting', 'sculpture', 'photography', 'video', 'performance', 'writing'],
                'career_level_min' => 'mid',
                'career_level_max' => 'established',
                'country' => 'IT',
                'description' => 'Residenza di 1 mese in Toscana. Studio, alloggio e accesso a community internazionale di artisti.',
                'source' => 'villa-lena.it',
                'verified' => true,
                'active' => true,
            ],
            [
                'title' => 'FID Prize — Drawing Today',
                'type' => 'prize',
                'deadline' => $now->copy()->addDays(25),
                'url' => 'https://www.fidprize.org',
                'mediums_accepted' => ['drawing'],
                'career_level_min' => 'emerging',
                'career_level_max' => 'mid',
                'country' => 'IT',
                'description' => 'Premio internazionale dedicato al disegno contemporaneo. Montepremi e mostra collettiva.',
                'source' => 'fidprize.org',
                'verified' => true,
                'active' => true,
            ],
            [
                'title' => 'Cittadellarte Fondazione Pistoletto — Open Call',
                'type' => 'residency',
                'deadline' => $now->copy()->addDays(100),
                'url' => 'https://www.cittadellarte.it',
                'mediums_accepted' => ['installation', 'performance', 'video', 'painting', 'sculpture'],
                'career_level_min' => 'emerging',
                'career_level_max' => 'established',
                'country' => 'IT',
                'description' => 'Programma UNIDEE di residenza e formazione presso Fondazione Pistoletto a Biella.',
                'source' => 'cittadellarte.it',
                'verified' => true,
                'active' => true,
            ],
            [
                'title' => 'Premio Combat Prize',
                'type' => 'prize',
                'deadline' => $now->copy()->addDays(55),
                'url' => 'https://www.premiocombat.it',
                'mediums_accepted' => ['painting', 'photography', 'sculpture', 'video', 'drawing'],
                'career_level_min' => 'emerging',
                'career_level_max' => 'mid',
                'country' => 'IT',
                'description' => 'Premio annuale con sezioni separate per medium. Esposizione finale a Livorno.',
                'source' => 'premiocombat.it',
                'verified' => true,
                'active' => true,
            ],
            [
                'title' => 'CCA Andratx Residency Mallorca',
                'type' => 'residency',
                'deadline' => $now->copy()->addDays(180),
                'url' => 'https://ccandratx.com',
                'mediums_accepted' => [],
                'career_level_min' => 'mid',
                'career_level_max' => 'established',
                'country' => 'ES',
                'description' => 'International residency program at Centre Cultural Andratx in Mallorca. Studio, accommodation, exhibition.',
                'source' => 'ccandratx.com',
                'verified' => true,
                'active' => true,
            ],
        ];

        foreach ($opportunities as $data) {
            Opportunity::updateOrCreate(
                ['title' => $data['title']],
                $data,
            );
        }
    }
}
