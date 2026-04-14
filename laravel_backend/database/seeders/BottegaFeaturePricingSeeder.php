<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-14
 * @purpose Registra strumenti La Bottega in core.ai_feature_pricing.
 *          Costi Egili = NULL (da definire da Fabio). NULL = strumento non ancora prezzato.
 *          Strumenti gratuiti hanno cost_egili = 0.
 *          Idempotente: usa updateOrInsert su feature_code.
 *
 * ESECUZIONE:
 *   php artisan db:seed --class=BottegaFeaturePricingSeeder
 *
 * SSOT: EGI-DOC/docs/la-bottega/06_ECONOMIA_EGILI.md
 */
class BottegaFeaturePricingSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            // --- Creator Tools ---
            [
                'feature_code' => 'bottega_microscopio',
                'feature_name' => 'La Bottega — Microscopio',
                'feature_description' => 'Report diagnostico completo del profilo artista: identità, completezza, coerenza, visibilità.',
                'feature_category' => 'bottega',
                'cost_fiat_eur' => null,
                'ai_tokens_included' => null,
                'egili_gift' => null,
                'cost_egili' => null,
                'max_uses_per_purchase' => null,
                'expires' => false,
                'duration_hours' => null,
                'is_recurring' => false,
                'recurrence_period' => null,
                'is_active' => true,
                'is_free' => false,
                'display_order' => 1,
            ],
            [
                'feature_code' => 'bottega_sestante',
                'feature_name' => 'La Bottega — Sestante',
                'feature_description' => 'Posizionamento artista vs comparabili per medium, stile, anzianità.',
                'feature_category' => 'bottega',
                'cost_fiat_eur' => null,
                'ai_tokens_included' => null,
                'egili_gift' => null,
                'cost_egili' => null,
                'max_uses_per_purchase' => null,
                'expires' => false,
                'duration_hours' => null,
                'is_recurring' => false,
                'recurrence_period' => null,
                'is_active' => true,
                'is_free' => false,
                'display_order' => 2,
            ],
            [
                'feature_code' => 'bottega_price_advisor',
                'feature_name' => 'La Bottega — Price Advisor',
                'feature_description' => 'Analisi prezzi via NPE Pricing Advisor. Stesso costo NPE.',
                'feature_category' => 'bottega',
                'cost_fiat_eur' => null,
                'ai_tokens_included' => null,
                'egili_gift' => null,
                'cost_egili' => 120,
                'max_uses_per_purchase' => null,
                'expires' => false,
                'duration_hours' => null,
                'is_recurring' => false,
                'recurrence_period' => null,
                'is_active' => true,
                'is_free' => false,
                'display_order' => 3,
            ],
            [
                'feature_code' => 'bottega_cantiere',
                'feature_name' => 'La Bottega — Cantiere',
                'feature_description' => 'Guida assistita per bio e artist statement. Consumo token-based.',
                'feature_category' => 'bottega',
                'cost_fiat_eur' => null,
                'ai_tokens_included' => null,
                'egili_gift' => null,
                'cost_egili' => null,
                'max_uses_per_purchase' => null,
                'expires' => false,
                'duration_hours' => null,
                'is_recurring' => false,
                'recurrence_period' => null,
                'is_active' => true,
                'is_free' => false,
                'display_order' => 4,
            ],
            [
                'feature_code' => 'bottega_coherence_check',
                'feature_name' => 'La Bottega — Coherence Check',
                'feature_description' => 'Analisi coerenza collezione via NPE CollectionSplitter.',
                'feature_category' => 'bottega',
                'cost_fiat_eur' => null,
                'ai_tokens_included' => null,
                'egili_gift' => null,
                'cost_egili' => null,
                'max_uses_per_purchase' => null,
                'expires' => false,
                'duration_hours' => null,
                'is_recurring' => false,
                'recurrence_period' => null,
                'is_active' => true,
                'is_free' => false,
                'display_order' => 5,
            ],
            [
                'feature_code' => 'bottega_binocolo',
                'feature_name' => 'La Bottega — Binocolo',
                'feature_description' => 'Ricerca opportunità filtrate: call for artists, fiere, residenze.',
                'feature_category' => 'bottega',
                'cost_fiat_eur' => null,
                'ai_tokens_included' => null,
                'egili_gift' => null,
                'cost_egili' => null,
                'max_uses_per_purchase' => null,
                'expires' => false,
                'duration_hours' => null,
                'is_recurring' => false,
                'recurrence_period' => null,
                'is_active' => true,
                'is_free' => false,
                'display_order' => 6,
            ],
            [
                'feature_code' => 'bottega_market_pulse',
                'feature_name' => 'La Bottega — Market Pulse',
                'feature_description' => 'Report trend e mercato per medium e segmento.',
                'feature_category' => 'bottega',
                'cost_fiat_eur' => null,
                'ai_tokens_included' => null,
                'egili_gift' => null,
                'cost_egili' => null,
                'max_uses_per_purchase' => null,
                'expires' => false,
                'duration_hours' => null,
                'is_recurring' => false,
                'recurrence_period' => null,
                'is_active' => true,
                'is_free' => false,
                'display_order' => 7,
            ],
            [
                'feature_code' => 'bottega_visibility_tracker',
                'feature_name' => 'La Bottega — Visibility Tracker',
                'feature_description' => 'Analytics piattaforma: visite, visualizzazioni, engagement.',
                'feature_category' => 'bottega',
                'cost_fiat_eur' => null,
                'ai_tokens_included' => null,
                'egili_gift' => null,
                'cost_egili' => 0,
                'max_uses_per_purchase' => null,
                'expires' => false,
                'duration_hours' => null,
                'is_recurring' => false,
                'recurrence_period' => null,
                'is_active' => true,
                'is_free' => true,
                'display_order' => 8,
            ],

            // --- Collector Tools ---
            [
                'feature_code' => 'bottega_lente',
                'feature_name' => 'La Bottega — Lente',
                'feature_description' => 'Credibility Score artista: storico, coerenza, certificazioni, attività.',
                'feature_category' => 'bottega',
                'cost_fiat_eur' => null,
                'ai_tokens_included' => null,
                'egili_gift' => null,
                'cost_egili' => null,
                'max_uses_per_purchase' => null,
                'expires' => false,
                'duration_hours' => null,
                'is_recurring' => false,
                'recurrence_period' => null,
                'is_active' => true,
                'is_free' => false,
                'display_order' => 9,
            ],
            [
                'feature_code' => 'bottega_registro',
                'feature_name' => 'La Bottega — Registro',
                'feature_description' => 'Storico trasparente: blockchain, COA, transazioni. Dati pubblici.',
                'feature_category' => 'bottega',
                'cost_fiat_eur' => null,
                'ai_tokens_included' => null,
                'egili_gift' => null,
                'cost_egili' => 0,
                'max_uses_per_purchase' => null,
                'expires' => false,
                'duration_hours' => null,
                'is_recurring' => false,
                'recurrence_period' => null,
                'is_active' => true,
                'is_free' => true,
                'display_order' => 10,
            ],
            [
                'feature_code' => 'bottega_bilanciere',
                'feature_name' => 'La Bottega — Bilanciere',
                'feature_description' => 'Comparazione prezzi: opere simili per medium, stile, dimensioni.',
                'feature_category' => 'bottega',
                'cost_fiat_eur' => null,
                'ai_tokens_included' => null,
                'egili_gift' => null,
                'cost_egili' => null,
                'max_uses_per_purchase' => null,
                'expires' => false,
                'duration_hours' => null,
                'is_recurring' => false,
                'recurrence_period' => null,
                'is_active' => true,
                'is_free' => false,
                'display_order' => 11,
            ],
            [
                'feature_code' => 'bottega_portafoglio',
                'feature_name' => 'La Bottega — Portafoglio',
                'feature_description' => 'Collection value tracker: valore stimato, trend per artista.',
                'feature_category' => 'bottega',
                'cost_fiat_eur' => null,
                'ai_tokens_included' => null,
                'egili_gift' => null,
                'cost_egili' => 0,
                'max_uses_per_purchase' => null,
                'expires' => false,
                'duration_hours' => null,
                'is_recurring' => false,
                'recurrence_period' => null,
                'is_active' => true,
                'is_free' => true,
                'display_order' => 12,
            ],
        ];

        foreach ($records as $record) {
            DB::table('ai_feature_pricing')->updateOrInsert(
                ['feature_code' => $record['feature_code']],
                array_merge($record, [
                    'updated_at' => now(),
                ])
            );

            $cost = $record['cost_egili'] !== null ? number_format($record['cost_egili']) : 'TBD';
            $this->command->info("✓ {$record['feature_code']} — Egili: {$cost}");
        }

        $this->command->info('BottegaFeaturePricingSeeder completato — 12 strumenti registrati.');
    }
}
