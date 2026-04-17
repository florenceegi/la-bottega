<?php

declare(strict_types=1);

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-17
 * @purpose Seed segnali di mercato starter per Market Pulse C.2.
 */

namespace Database\Seeders;

use App\Models\MarketTrend;
use Illuminate\Database\Seeder;

class MarketTrendSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $from = $now->copy()->subMonths(3)->toDateString();
        $to = $now->copy()->toDateString();

        $trends = [
            [
                'signal_key' => 'photography_emerging_demand_2026q1',
                'category' => 'demand',
                'medium' => 'photography',
                'career_level' => 'emerging',
                'region' => 'IT',
                'direction' => 'rising',
                'magnitude' => 'moderate',
                'insight' => 'Domanda in crescita per fotografia contemporanea di artisti emergenti italiani, specialmente su tematiche identitarie e paesaggistiche.',
                'actionable_advice' => 'Valuta partecipazione a fiere fotografiche (MIA Photo Fair) e prezzi tra 500-1500 EUR per stampe in edizione limitata (1/5).',
                'source' => 'FlorenceEGI Market Observatory',
            ],
            [
                'signal_key' => 'painting_mid_career_stable_2026q1',
                'category' => 'price',
                'medium' => 'painting',
                'career_level' => 'mid',
                'region' => 'IT',
                'direction' => 'stable',
                'magnitude' => 'small',
                'insight' => 'Segmento pittura mid-career stabile: acquirenti attendono consolidamento curriculum prima di acquistare sopra 3000 EUR.',
                'actionable_advice' => 'Mantieni prezzi attuali, investi in partecipazioni collettive curate e pubblicazioni critiche per giustificare futuri incrementi.',
                'source' => 'FlorenceEGI Market Observatory',
            ],
            [
                'signal_key' => 'digital_art_rising_global_2026q1',
                'category' => 'demand',
                'medium' => 'digital',
                'career_level' => null,
                'region' => null,
                'direction' => 'rising',
                'magnitude' => 'large',
                'insight' => 'Arte digitale certificata blockchain in forte crescita globale, con interesse crescente anche da collezionisti tradizionali.',
                'actionable_advice' => 'Considera edizioni ibride (fisico + certificato digitale) per ampliare bacino collezionisti senza competere con artisti puramente digitali.',
                'source' => 'FlorenceEGI Market Observatory',
            ],
            [
                'signal_key' => 'sculpture_installation_opportunity_2026q1',
                'category' => 'opportunity',
                'medium' => 'sculpture',
                'career_level' => null,
                'region' => 'EU',
                'direction' => 'rising',
                'magnitude' => 'moderate',
                'insight' => 'Bandi europei pubblici per scultura e installazione site-specific in aumento (rigenerazione urbana, PNRR cultura).',
                'actionable_advice' => 'Monitora bandi comunali e regionali per opere permanenti. Usa Binocolo per scoprire call attive.',
                'source' => 'FlorenceEGI Market Observatory',
            ],
            [
                'signal_key' => 'drawing_works_on_paper_entry_2026q1',
                'category' => 'price',
                'medium' => 'drawing',
                'career_level' => 'emerging',
                'region' => null,
                'direction' => 'rising',
                'magnitude' => 'small',
                'insight' => 'Disegni e opere su carta rappresentano entry-point preferito da nuovi collezionisti (300-800 EUR).',
                'actionable_advice' => 'Mantieni una produzione attiva su carta: acquirente che inizia con disegno spesso torna per pittura dopo 6-12 mesi.',
                'source' => 'FlorenceEGI Market Observatory',
            ],
        ];

        foreach ($trends as $trend) {
            MarketTrend::updateOrCreate(
                ['signal_key' => $trend['signal_key']],
                array_merge($trend, [
                    'observed_from' => $from,
                    'observed_to' => $to,
                    'active' => true,
                ])
            );
        }
    }
}
