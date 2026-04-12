<?php

/**
 * @package Database\Seeders
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-12
 * @purpose Seeder ruoli Spatie RBAC per La Bottega
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'artist' => 'Artista — accesso strumenti creator, percorsi, Maestro Creator',
            'collector' => 'Collezionista — accesso strumenti collector, Maestro Collector',
            'event_organizer' => 'Organizzatore eventi — gestione opportunita, call for artists',
            'gallery' => 'Galleria — gestione artisti rappresentati, analytics',
        ];

        foreach ($roles as $name => $description) {
            Role::findOrCreate($name, 'web');
        }

        $permissions = [
            'maestro.chat',
            'tools.microscopio',
            'tools.sestante',
            'tools.price_advisor',
            'tools.cantiere',
            'tools.coherence_check',
            'tools.binocolo',
            'tools.market_pulse',
            'tools.visibility_tracker',
            'tools.lente',
            'tools.registro',
            'tools.bilanciere',
            'tools.portafoglio',
            'percorso.view',
            'percorso.advance',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $artistRole = Role::findByName('artist');
        $artistRole->givePermissionTo([
            'maestro.chat',
            'tools.microscopio',
            'tools.sestante',
            'tools.price_advisor',
            'tools.cantiere',
            'tools.coherence_check',
            'tools.binocolo',
            'tools.market_pulse',
            'tools.visibility_tracker',
            'percorso.view',
            'percorso.advance',
        ]);

        $collectorRole = Role::findByName('collector');
        $collectorRole->givePermissionTo([
            'maestro.chat',
            'tools.lente',
            'tools.registro',
            'tools.bilanciere',
            'tools.portafoglio',
        ]);
    }
}
